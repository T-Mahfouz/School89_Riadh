<div class="modal fade" tabindex="-1" role="dialog" id="write-msg">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Write Message</h4>
      </div>
      <div class="modal-body">
        <form>
          {{csrf_field()}}
          <textarea class="form-control" name="msg-body" id="msg-body" rows="5" placeholder="Write your message here..." ></textarea>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="send-msg" type="button" class="btn btn-primary">Send</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->