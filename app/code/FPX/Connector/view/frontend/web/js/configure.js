define([
    'jquery',
    'cpqModal',
    'mage/translate',
    'underscore'
], function ($, cpqModal, $t, _) {
    'use strict';

    $.widget('fpx.cpqButton', cpqModal, {

        /**
         * @private
         * @override
         */
        _initLocalVariables: function () {
            this._super();

            this.authUrl = this.options.authUrl;
            this.requestOptions = this.options.requestOptions;
            this.integrationOptions = this.options.integrationOptions;

            this.cartActionUrl = this.options[this.integrationOptions.action + 'CartUrl'];
        },

        /**
         * @private
         * @override
         */
        _beforeOpenModal: function () {
            this._super();

            this.hideFrame();

            var data = this._prepareConfigData();
            // retrieve token with request options and proceed
            $.ajax(data);
        },

        /**
         * @description
         * Create config Data
         *
         * @return Object
         */
        _prepareConfigData: function () {
            var data = {
                url: this.authUrl,
                type: 'get',
                data: this.requestOptions,
                showLoader: true,
                success: this._successResponse.bind(this),
                error: this._errorResponse.bind(this)
            };

            return data;
        },

        /**
         * @description
         * Prepare iframe and show it
         */
        _successResponse: function (response) {
            if (response.status == 'error') {
                this._processInternalError();
            } else {
                this._processCPQ(response.data);
                this.showFrame();
            }
        },

        /**
         * @description
         * Reload Page if has errors
         */
        _processInternalError: function () {
            location.reload();
        },

        /**
         * @description
         * Hide Modal if has errors
         */
        _errorResponse: function () {
            this._hideModal();
        },

        /**
         * @description
         * Generate URL for iframe and append it to page
         */
        _processCPQ: function (token) {
            var iframeUrl   = this.integrationOptions.iframeUrl;
            var sku         = this.integrationOptions.sku;
            var dataSet     = this.integrationOptions.dataSet;
            var action      = this.integrationOptions.action;

            var internalParams = '/appConfig/' + sku + '/' + action + '/customize?ds=' + dataSet;
            var url = iframeUrl + '?anonymous=' + encodeURIComponent(token) + '#' + internalParams;

            var modalElement = this._getModalElement();
            modalElement.append('<iframe src="' + url + '"></iframe>');
        },

        /**
         * @private
         * @override
         */
        _handleReceiveMessage: function (event) {
            var cpqData = this._processPostMessage(event.originalEvent.data);

            if (this.options.useFormData) {
                var formData = this._getFormData();
                var cpqData = $.merge(formData, cpqData);
            }

            if (this.integrationOptions.action == "edit") {
                var oldConfig = [{
                    name: this.integrationOptions.productDataPrefix + 'old_config',
                    value: this.integrationOptions.sku
                }];
                var cpqData = $.merge(oldConfig, cpqData);
            }

            this._sendProductData(cpqData);
        },

        _processPostMessage: function (data) {
            var result = [];
            var jsonData = {};

            try {
                jsonData = $.parseJSON(data);
            } catch (error) {
                console.log(error.message);
            }

            if ($.isEmptyObject(jsonData)) {
                return result;
            }

            if (jsonData['action'] != "added") {
                this._processError('Some problems with CPQ product adding.');
            }

            for (var i in jsonData) {
                result.push(
                    {
                        "name": this.integrationOptions.productDataPrefix + '[' + i + ']',
                        "value": jsonData[i]
                    }
                );
            }

            return result;
        },

        _getFormData: function () {
            var form = $(this.options.formElement);
            if (form.length == 0 || !form.valid()) {
                this._processError('Form is invalid.');
            }

            return form.serializeArray();
        },

        _sendProductData: function (data) {
            var self = this;

            $.ajax({
                url: this.cartActionUrl,
                data: data,
                type: 'post',
                dataType: 'json',
                success: this._successCartResponse.bind(this),
                error: this._errorCartResponse.bind(this)
            });
        },

        _successCartResponse: function (res) {
            if (res && res.backUrl) {
                window.location = res.backUrl;
                return;
            }

            if (this.options.redirect) {
                window.location = this.options.redirect;
                return;
            }

            if (this.options.refreshPage) {
                location.reload();
            }
            this._hideModal();
        },

        /**
         * @description
         * Hide Modal if has errors
         */
        _errorCartResponse: function (message) {
            this._hideModal();
            console.log(message);
        },

        /**
         * @description
         * Hide Modal if has any generic errors
         */
        _processError: function (message) {
            this._hideModal();
            throw new Error(message);
        },

        /**
         * @description
         * Show CPQ iframe
         */
        showFrame: function () {
            this._getModalElement().find('iframe').show();
        },

        /**
         * @description
         * Hide CPQ iframe
         */
        hideFrame: function () {
            this._getModalElement().find('iframe').hide();
        }
    });

    return $.fpx.cpqButton;
});

