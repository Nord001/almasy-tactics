<?

//----------------------------------------------------------------------------------------------
class AppHelper extends Helper {

    //----------------------------------------------------------------------------------------------
    // $dest can be an old-fashioned array, or can be a string of the form CONTROLLER/ACTION.
    // This form can have additional arguments, which represent the extra arguments.
    function link2 ($linkContent, $dest) {
        if (is_array($dest)) {
            $controller = @$dest['controller'];
            $action = @$dest['action'];

            if ($controller === '')
                $controller = $this->params['controller'];

            $dest = array_splice($dest, 2);

            $href = $controller;
            if ($action != 'index')
                $href .= '/' . $action;

            if (count($dest) > 0)
                $href .= '/' . implode('/', $dest);
        } else {
            $args = func_get_args();
            $args = array_splice($args, 2);

            $href = $dest;
            if (count($args) > 0)
                $href .= '/' . implode('/', $args);

            if ($href{0} == '/')
                $href = substr($href, 1);
        }

        return sprintf("<a href = '/%s'>%s</a>", $href, $linkContent);
    }
}

?>
