define('file-sharing:views/record/panels/information', ['views/record/panels/side'], (Dep) => {
    return class extends Dep {

        fieldList = [
            'entryPointUrl',
            'accessToken',
            'accessCount',
        ]

        setup() {
            super.setup();

            this.listenTo(this.model, 'newAccessToken', () => {
                this.model.fetch();
            });
        }

    };
});