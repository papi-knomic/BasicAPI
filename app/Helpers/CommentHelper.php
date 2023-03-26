<?php

use App\Models\Comment;

if ( !function_exists('checkCommentCreator') ) {
    function checkCommentCreator( Comment $comment ) : bool
    {
        return $comment->user_id === auth()->id();
    }
}
