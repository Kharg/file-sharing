define('file-sharing:token-handler', ['action-handler'], function (Dep) {

    return Dep.extend({
  
      actionGenerateNewToken: function () {
        this.confirm(this.view.translate('confirmation', 'messages'), () => {
            this.ajaxPostRequest('FileSharing/action/GenerateNewToken', {
                id: this.view.model.id
            }).then(response => {
                Espo.Ui.success(this.view.translate('Done'));
            });
        });
      },
  
      initGenerateToken: function () {
        this.controlButtonVisibility();
  
        this.view.listenTo(
          this.view.model,
          'change:accessToken',
          this.controlButtonVisibility.bind(this)
        );
      },
  
      controlButtonVisibility: function () {
        if (this.view.model.get('accessToken') != null) {
          this.view.showActionItem('GenerateNewToken');
        } else {
          this.view.hideActionItem('GenerateNewToken');
        }
      },
    });
  });