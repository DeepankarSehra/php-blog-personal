<?php
require_once 'lib/common.php';
require_once 'lib/view_post.php';

//  get post id
if(isset($_GET['post_id']))
{
    $postId = $_GET['post_id'];
}
else
{
    $postId = 0;
}

$pdo = getPDO();
$row = getPostRow($pdo, $postId);

if(!$row){
    redirectAndExit('index.php?not-found=1');
}

$errors = null;
if($_POST){
    $commentData = array(
        '_name' => $_POST['comment-name'],
        '_text' => $_POST['comment-text'],
        'website' => $_POST['comment-website'],
    );

    $errors = addCommentToPost($pdo, $postId, $commentData);

    if(!$errors){
        redirectAndExit('view_post.php?post_id=' . $postId);
    }
}
else
{
    $commentData = array('_name' => '', 'website' => '', '_text' => '');
}


?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            A Blog application | <?php echo htmlEscape($row['title']) ?>
        </title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    </head>

        <?php require 'templates/title.php'?>
        <h2>
            <?php echo htmlEscape($row['title']) ?>
        </h2>
        <div>
            <?php echo convertSqlDate($row['created_at']) ?>
        </div>
        <p>
            <?php echo convertNewLinesToParagraphs($row['body']) ?>
        </p>
        
        <h3>
            <?php echo countCommentsForPost($postId) ?> comments 
        </h3>

        <?php foreach (getCommentsForPost($postId) as $comment): ?>
        <hr>
        <div class="comment">
            <div class="comment-meta">
                Comment from 
                <?php echo htmlEscape($comment['_name']) ?>
                on
                <?php echo convertSqlDate($comment['created_at']) ?>
            </div>
            <div class="comment-body">
                <?php echo convertNewLinesToParagraphs($comment['_text']) ?>
            </div>
        </div>
        <?php endforeach ?>

        <?php require 'lib/comment_form.php' ?>

    </body>
</html>

