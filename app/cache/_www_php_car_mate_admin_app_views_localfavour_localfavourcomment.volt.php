<?php if (empty($comments)) { ?>
<h4>暂无评论</h4>
<?php } else { ?>
<table class="table" style="width:100%">
    <thead><th style="width:5%">评论人</th><th style="width:20%">评论时间</th><th style="width:35%">评论内容</th><th style="width:35%">回复内容</th><th style="width:5%">操作</th></thead>
    <tbody>
    <?php foreach ($comments as $comment) { ?>
    <tr>
        <td><?php echo $comment->userId; ?></td>
        <td><?php echo date('Y-m-d H:i:s', strtotime($comment->commentTime)); ?></td>
        <td><?php echo $comment->comment; ?></td>
        <td class="comment-reply" data-id="<?php echo $comment->id; ?>"><?php echo $comment->commentReply; ?></td>
        <td><a data-id="<?php echo $comment->id; ?>" class="btn btn-mini show-reply"><i class="icon-comment"></i></a></td>
    </tr>
    <tr style="display:none" class="do-reply" data-id="<?php echo $comment->id; ?>">
        <td colspan="5">
            <div class="row-fluid">
                <textarea name="content" class="reply-content span12" data-id="<?php echo $comment->id; ?>" cols="30" rows="5"></textarea>
            </div>
            <div>
                <a data-id="<?php echo $comment->id; ?>" class="btn btn-small btn-primary pull-right do-reply-btn"><i class="icon-comment"></i>回复</a>
            </div>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>
<?php } ?>
<script>
    (function($){
        //记录当前在显示的回复文本域
        var current_show = null;
        //显示回复文本域的按钮点击事件
        $('.show-reply').click(function(event){
            event.preventDefault();
            if(current_show) current_show.hide();
            var comment_id = $(this).attr('data-id');
            current_show = $('.do-reply[data-id=' + comment_id +']').show('fast');
            return false;
        });
        //回复按钮事件
        $('.do-reply-btn').click(function(event){
            event.preventDefault();
            var comment_id = $(this).attr('data-id');
            var content = $('.reply-content[data-id=' + comment_id +']').val();
            $.ajax({
                url: '/localFavourCommentReply/' + comment_id + '.json',
                method: 'PUT',
                data: {content: content},
                dataType: 'json',
                success: function(data){
                    if(data.success)
                    {
                        $('.comment-reply[data-id=' + data.id +']').text(data.content);
                    }
                }
            });
            return false;
        });
    })(jQuery);
</script>