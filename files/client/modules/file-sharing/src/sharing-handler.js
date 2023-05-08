define('file-sharing:sharing-handler', ['action-handler'], function (Dep) {

  return Dep.extend({

    actionOpenUrl: function () {
      let url = this.view.model.get('entryPointUrl');
      window.open(url);
      this.notify(false);
    },

    initFileSharing: function () {
      this.controlButtonVisibility();

      this.view.listenTo(
        this.view.model,
        'change:attachmentId',
        this.controlButtonVisibility.bind(this)
      );
    },

    controlButtonVisibility: function () {
      if (this.view.model.get('attachmentId') != null) {
        this.view.showHeaderActionItem('OpenUrl');
      } else {
        this.view.hideHeaderActionItem('OpenUrl');
      }
    },
  });
});