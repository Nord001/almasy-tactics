<?

define('NEWS_CACHE', 'home_news');
define('NEWS_CACHE_DURATION', 'long');

class News extends AppModel {

    var $belongsTo = array(
        'User',
    );

    //--------------------------------------------------------------------------------------------
    function afterSave ($created) {
        CacheDelete(NEWS_CACHE);
    }

    //--------------------------------------------------------------------------------------------
    function afterDelete () {
        CacheDelete(NEWS_CACHE);
    }

    //--------------------------------------------------------------------------------------------
    function GetLatestNews () {
        $news = CacheRead(NEWS_CACHE);
        if ($news !== false)
            return $news;

        $news = $this->find('all', array(
            'order' => 'News.id DESC',
            'limit' => 4,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.username',
                    ),
                ),
            ),
        ));
        CacheWrite(NEWS_CACHE, $news);

        return $news;
    }
};

?>