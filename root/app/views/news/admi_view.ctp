<h2>News</h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
    </tr>
    <tr>
        <td>Title</td>
        <td><?= $news['News']['title']; ?></td>
    </tr>
    <tr>
        <td>Content</td>
        <td><?= $news['News']['content']; ?></td>
    </tr>
    <tr>
        <td>Date Posted</td>
        <td><?= $news['News']['date_posted']; ?></td>
    </tr>
    <tr>
        <td>User Id</td>
        <td><?= $news['News']['user_id']; ?></td>
    </tr>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit News', array('action' => 'edit', $news['News']['id'])); ?> </li>
        <li><?= $html->link('Delete News', array('action' => 'delete', $news['News']['id']), null, 'Are you sure you want to delete this news?'); ?> </li>
    </ul>
</div>
