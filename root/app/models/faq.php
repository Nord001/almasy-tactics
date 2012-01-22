<?

define('FAQ_CACHE', 'faqs');
define('FAQ_CACHE_DURATION', 'long');

define('FAQS_BY_PATH_CACHE', 'faqs_by_path');
define('FAQS_BY_PATH_CACHE_DURATION', 'long');

define('FAQS_BY_CATEGORY_CACHE', 'faqs_by_category');
define('FAQS_BY_CATEGORY_CACHE_DURATION', 'long');

class Faq extends AppModel {

    var $belongsTo = array(
        'User',
    );

    //--------------------------------------------------------------------------------------------
    function GetFaqsForPath ($path) {
        $faqsByPath = Cache::read(FAQS_BY_PATH_CACHE, FAQS_BY_PATH_CACHE_DURATION);

        if ($faqsByPath == false) {
            $faqs = $this->GetAllFaqs();
            $faqsByPath = array();
            foreach ($faqs as $faq) {
                if ($faq['Faq']['path'] !== '')
                    $faqsByPath[$faq['Faq']['path']][] = $faq;
            }

            Cache::write(FAQS_BY_PATH_CACHE, $faqsByPath, FAQS_BY_PATH_CACHE_DURATION);
        }

        return isset($faqsByPath[$path]) ? $faqsByPath[$path] : false;
    }

    //--------------------------------------------------------------------------------------------
    function GetFaqsByCategory () {
        $faqsByCategory = Cache::read(FAQS_BY_CATEGORY_CACHE, FAQS_BY_CATEGORY_CACHE_DURATION);

        if ($faqsByCategory == false) {
            $faqs = $this->GetAllFaqs();
            $faqsByCategory = array();
            foreach ($faqs as $faq) {
                if ($faq['Faq']['category'] !== '')
                    $faqsByCategory[$faq['Faq']['category']][] = $faq;
                else
                    $faqsByCategory['Other'][] = $faq;
            }

            ksort($faqsByCategory);

            Cache::write(FAQS_BY_CATEGORY_CACHE, $faqsByCategory, FAQS_BY_CATEGORY_CACHE_DURATION);
        }

        return $faqsByCategory;
    }

    //--------------------------------------------------------------------------------------------
    function GetAllFaqs () {
        $faqs = Cache::read(FAQ_CACHE, FAQ_CACHE_DURATION);
        if ($faqs)
            return $faqs;

        $faqs = $this->find('all');

        Cache::write(FAQ_CACHE, $faqs, FAQ_CACHE_DURATION);

        return $faqs;
    }
};

?>