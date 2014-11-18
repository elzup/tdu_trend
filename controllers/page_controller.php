<?php


class PageController {

    public function showIndex() {
        $title = '電大トレンド君 on Web';
        require_once('./views/head.php');
        require_once('./views/body_wrapper_head.php');
        require_once('./views/toppage_eyecatch.php');
        require_once('./views/body_wrapper_foot.php');
        require_once('./views/foot.php');
    }
}
