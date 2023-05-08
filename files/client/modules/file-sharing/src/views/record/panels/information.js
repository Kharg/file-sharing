define('file-sharing:views/record/panels/information', 'views/record/panels/side', function (Dep) {

    return Dep.extend({

        fieldList: [
            'entryPointUrl',
            'accessToken',
            'accessCount',
        ],

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:accessToken', () => {
                this.model.fetch();
            });
        },

    });

});
