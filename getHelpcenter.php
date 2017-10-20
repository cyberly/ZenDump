<?php
/**
*   Helpcenter articles and shit.
*
*	This is probably going to be an utter shitshow.
*
*	@author 	cbyerly <cbyerly@liquidweb.com>
*	@license	MIT maybe idk
*	@package	ZenDump
*	@link 		https://git.liquidweb.com/cbyerly/ZenDump
*
*	¯\_(ツ)_/¯
*/
namespace ZenDump;
require 'vendor/autoload.php';
include("inc/curl.inc.php");
include("inc/database.inc.php");
include("inc/models.inc.php");

set_time_limit(0);
$startTime = microtime(true);
$prod = new zdCurl("production");
$lastPage = FALSE;
$endpoint = "/help_center/articles.json?per_page=50";


while(!$lastPage){
    $data = $prod->get($endpoint)->response;
    $articles = $data["articles"];
    foreach ($articles as $a){
        $article = Article::find($a["id"]);
        if ($article === NULL){
            $article = new Article;
            $article->article_id = $a["id"];
        }
        if (!empty($a["outdated_locales"])){
            $a["outdated_locales"] = implode(', ', $a["outdated_locales"]);
        }
        if (!empty($a["label_names"])){
            $a["label_names"] = implode(', ', $a["label_names"]);
        }
        unset($a["id"]);
        unset($a["manageable_by"])
        foreach ($a as $k => $v){
            if (!empty($v)){
                $article->$k = $v;
            }
        }
        $attachmentData = $prod->get("/help_center/articles/" . $article->article_id . "/attachments.json")->response;
        if (!empty($attachmentData["article_attachments"])){
            foreach ($attachmentData["article_attachments"] as $att){
                $attachment = new ArticleAttachment;
                $attachment->attachment_id = $att["id"];
                unset($att["id"]);
                foreach ($att as $k => $v){
                    if (!empty($v)){
                        $attachment->$k = $v;
                    }
                }
                $attachment->save();
            }
        }
        $article->save();
    }
    if (!$data["next_page"]){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}

$lastPage = FALSE;
$endpoint = "/help_center/categories.json?per_page=50";
$data = $prod->get($endpoint)->response;
$categories = $data["categories"];
foreach ($categories as $c){
    $category = new Category;
    $category->category_id = $c["id"];
    unset($c["id"]);
    foreach ($c as $k => $v){
        if (!empty($v)){
            $category->$k = $v;
        }
    }
    $category->save();
}

$endpoint = "/help_center/sections.json?per_page=50";
while(!$lastPage){
    $data = $prod->get($endpoint)->response;
    $sections = $data["sections"];
    foreach($sections as $s){
        $section = new Section;
        $section->section_id = $s["id"];
        unset($s["id"]);
        foreach ($s as $k => $v){
            if (!empty($v)){
                $section->$k = $v;
            }
        }
        $section->save();
    }

    if (!$data["next_page"]){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}
