<?

define('HELP_COMMENTS_BY_PAGE_CACHE', 'help_comments');

class HelpComment extends AppModel {

    var $knows = array('User');

    //---------------------------------------------------------------------------------------------
    function ClearCommentsByPageCache ($page) {
        CheckNumeric($page);

        $cacheKey = GenerateCacheKey(HELP_COMMENTS_BY_PAGE_CACHE, $page);
        CacheDelete($cacheKey);
    }

    //---------------------------------------------------------------------------------------------
    function GetCommentsByHelpPage ($page, $mode) {
        if ($mode != 'date' && $mode != 'rating') {
            IERR('Invalid mode.');
            return false;
        }

        $cacheKey = GenerateCacheKey(HELP_COMMENTS_BY_PAGE_CACHE, $page, $mode);
        $comments = CacheRead($cacheKey);
        if ($comments)
            return $comments;

        $sort = false;
        if ($mode == 'date')
            $sort = 'time ASC';
        else if ($mode == 'rating')
            $sort = 'rating DESC';

        $comments = $this->find('all', array(
            'conditions' => array(
                'HelpComment.help_page' => $page,
            ),
            'order' => $sort,
        ));

        // Collate by threads
        if ($mode == 'date') {
            $ids = Set::classicExtract($comments, '{n}.HelpComment.id');
            $indexesById = array_flip($ids);

            for ($i = 0; $i < count($comments); $i++) {
                if (is_numeric($comments[$i]['HelpComment']['parent_id'])) {
                    $parentComment =& $comments[$indexesById[$comments[$i]['HelpComment']['parent_id']]];
                    if (!isset($parentComment['comments']))
                        $parentComment['comments'] = array();
                    $parentComment['comments'][] = $comments[$i];
                    unset($comments[$i]);
                    $i--;
                }
            }
        }

        foreach ($comments as &$comment) {
            $user = $this->User->GetUser($comment['HelpComment']['user_id']);
            $comment['HelpComment']['User'] = $user['User'];
        }

        CacheWrite($cacheKey, $comments);
        return $comments;
    }
}

?>
