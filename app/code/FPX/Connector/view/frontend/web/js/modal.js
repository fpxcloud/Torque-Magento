define([
    'jquery',
    'mage/translate',
    'underscore'
], function ($, $t, _) {
    'use strict';

    $.widget('fpx.cpqModal', {

        /**
         * @description
         * default options
         *
         * @type {Object}
         */
        options: {
            authUrl: '',
            integrationOptions: {},
            requestOptions: {},
            modalOptions: {
                autoOpen: false,
                responsive: true,
                modalClass: 'fpx-configure-modal',
                title: $t('Configure'),
                closeText: $t('Close'),
                buttons: []
            }
        },

        /**
         * @private
         */
        _create: function () {
            var self = this;
            this.options.modalOptions.closed = function () {
                self._onModalClosed();
            };
            this._bind();
            this._initLocalVariables();
        },

        /**
         * @description
         * set event handlers
         *
         * @private
         */
        _bind: function () {
            var self = this;
            this._on({
                'click': 'handleClickEvent'
            });
            // disable the focus on element when jquery UI dialog is closed
            this.element.on('focus', function () {
                self.element.blur();
            });
        },

        /**
         * @description
         * The 'click' event handler.
         *
         * @param {Object} event
         */
        handleClickEvent: function (event) {
            this._showModal();
        },

        _initLocalVariables: function () {

        },

        _onModalClosed: function () {
            // unsubscribe from events:
            $(window).off('message onmessage');
            this._removeModal();
        },

        /**
         * @description
         * initialize CPQ block for the modal
         *
         * @private
         */
        _showModal: function () {
            this._initModal();
            var modalElement = this._getModalElement();
            modalElement.modal(this.options.modalOptions);
            this._beforeOpenModal();
            modalElement.modal('openModal');
            this._afterOpenModal();
        },

        _initModal: function () {
            var editor = $('<div id="' + this.options.integrationOptions.divId + '" class="cpq-container"></div>');
            editor.appendTo('body');
        },

        _hideModal: function () {
            var modalElement = this._getModalElement();
            if (modalElement.length) {
                modalElement.modal('closeModal');
            }
            this._removeModal();
        },

        _removeModal: function () {
            var modalElement = this._getModalElement();
            if (modalElement.length) {
                modalElement.remove();
            }
        },

        /**
         * @returns {*|jQuery|HTMLElement}
         * @private
         */
        _getModalElement: function () {
            return $('#' + this.options.integrationOptions.divId);
        },

        _beforeOpenModal: function () {

        },

        _afterOpenModal: function () {
            var self = this;
            // listen to CPQ response
            $(window).on(
                'message onmessage',
                $.proxy(self._handleReceiveMessage, self)
            );
        },

        _handleReceiveMessage: function (event) {

        },

    });

    return $.fpx.cpqModal;
});

