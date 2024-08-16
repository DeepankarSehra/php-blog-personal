<?php 
    function getRootPath(){
        return realpath(__DIR__ . '/..');
    }

    function getDatabasePath() {
        return getRootPath() . '/data/data.sqlite';
    }

    function getDSN() { 
        return 'sqlite:' . getDatabasePath();
    }

    function getPDO() {
        return new PDO(getDSN());
    }

    function htmlEscape($html){
        return htmlspecialchars($html, ENT_HTML5, "UTF-8");
    }
    
    function convertSqlDate($sqlDate) {
        $date = DateTime::createFromFormat('Y-m-d', $sqlDate);
        return $date->format('d M Y');
    }
    
    function countCommentsForPost($postId)
    {
        $pdo = getPDO();
        $sql = "SELECT COUNT(*) c FROM comment WHERE post_id = :post_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            array('post_id' => $postId, )
        );
        return (int) $stmt->fetchColumn();
    }

    
    function getCommentsForPost($postId)
    {
        $pdo = getPDO();
        $sql = "SELECT id, _name, _text, created_at, website FROM comment WHERE post_id = :post_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            array('post_id' => $postId,)
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    function redirectAndExit($script)
    {
        // Get the domain-relative URL (e.g. /blog/whatever.php or /whatever.php) and work
        $relativeUrl = $_SERVER['PHP_SELF'];
        $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);
        // Redirect to the full URL (http://myhost/blog/script.php)
        $host = $_SERVER['HTTP_HOST'];
        $fullUrl = 'http://' . $host . $urlFolder . $script;
        header('Location: ' . $fullUrl);
        exit();
    }

    function getSqlDateForNow()
    {
        return date('Y-m-d');
    }

    function convertNewLinesToParagraphs($text)
    {
        $escaped = htmlEscape($text);
        return '<p>' . str_replace("\n", "<p></p>", $escaped) . '</p>';
    }

    
?>