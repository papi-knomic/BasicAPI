<?php

use App\Models\Comment;

if ( !function_exists('isCommentCreator') ) {
    function isCommentCreator( Comment $comment ) : bool
    {
        return $comment->user_id === auth()->id();
    }
}
