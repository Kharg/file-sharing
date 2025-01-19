define('file-sharing:sharing-handler', ['action-handler'], (Dep) => {
  return class extends Dep {

    openUrl() {
      let url = this.view.model.get('entryPointUrl');
      window.open(url);
      this.view.notify(false);
    }

    isOpenUrlVisible() {
      return this.view.model.get('attachmentId') != null;
  }
  
  };
});