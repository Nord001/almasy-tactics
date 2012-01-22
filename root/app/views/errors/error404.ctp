<h2>Not Found </h2>

<p class = 'error'>
    <strong>Error: </strong>
    <?= sprintf('The requested address %s was not found on this server. Maybe you\'re trying to access something that doesn\'t exist, or maybe you were trying to do something you shouldn\'t be doing.. If you were trying to do something normal and this happened, just tell me and I\'ll see what I can do.', "<strong>'{$message}'</strong>"); ?>
</p>

<p class = 'error'>
    <a href = 'javascript: history.back()'>Back to previous page!</a>
</p>