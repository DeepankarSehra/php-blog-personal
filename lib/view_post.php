<?php 
function getPostRow(PDO $pdo, $postId)
{
    $stmt = $pdo->prepare('SELECT title, created_at, body FROM post WHERE id=:id');
    if ($stmt === false) {
        throw new Exception('there was a problem preparing this query');
    }

    $result = $stmt->execute
    (
        array('id' => $postId,)
    );

    if ($result === false) {
        throw new Exception('there was a problem running this query');
    }

    //  lets get a row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}

// writing a comment to a particular post
function addCommentToPost(PDO $pdo, $postId, array $commentData)
{   
    $errors = array();

    // validation
    if(empty($commentData['_name'])) {
        $errors['_name'] = 'A name is required';
    }
    if(empty($commentData['_text'])) {
        $errors['_title'] = "A comment is required";
    }

    // if error free, try writing the comment
    if(!$errors){
        $sql = "INSERT INTO comment (_name, _text, website, created_at, post_id) VALUES (:_name, :_text, :website, :created_at, :post_id) ";
        $stmt = $pdo->prepare($sql);
        if($stmt === false){
            throw new Exception("Cannot prepare statement to insert comment");
        }

        
        $result = $stmt->execute(array_merge($commentData, array('post_id' => $postId, 'created_at' => getSqlDateForNow())));
        if ($result === false) {
            $errorInfo = $stmt->errorInfo();
            if($errorInfo){
                $errors[] = $errorInfo[2];
            }
        }
    }
    return $errors;
}

?>