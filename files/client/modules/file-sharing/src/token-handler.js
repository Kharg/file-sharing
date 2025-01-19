define('file-sharing:token-handler', ['action-handler'], (Dep) => {
    return class extends Dep {
  
      generateNewToken() {
        this.view.confirm(this.view.translate('confirmation', 'messages'), () => {
          Espo.Ajax.postRequest('FileSharing/action/GenerateNewToken', {
              id: this.view.model.id
          }).then(response => {
              Espo.Ui.success(this.view.translate('Done'));
              //this.view.model.fetch();
              this.view.model.trigger('newAccessToken');
          });
      });
      }
  
      initGenerateNewToken() {}
  
      isGenerateNewTokenVisible() {
        return this.view.model.get('accessToken') != null;
    }
    
    };
  });