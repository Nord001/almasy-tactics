<?

//----------------------------------------------------------------------------------------------
/**
 * Determines if a given string expression is true given the variables.
 *
 * @param string $expr Expression to be evaluated.
 * @param array $variables The variables to use in evaluation.
 * @return boolean True if the string expression was true.
 */
function ObeysExpr ($expr, $variables) {
    if (strlen($expr) == 0)
        return true;

    if (preg_match('/[^a-zA-Z0-9&|<>= ]/', $expr) > 0) {
        IERR('Invalid expression.', $expr);
        return false;
    }

    preg_match_all('/[a-zA-Z]+/', $expr, $matches);
    foreach ($matches[0] as $var) {
        if (!isset($variables[$var])) {
            IERR('Unknown variable in expression.', array('expr' => $expr, 'var' => $var));
            return false;
        }
    }

    $expr = preg_replace('/([a-zA-Z]+)/', '$\1', $expr);

    extract($variables, EXTR_SKIP);

    $result = eval('return ' . $expr . ';');
    if ($result == NULL)
        return false;

    if ($result)
        return true;
    else
        return false;
}

?>