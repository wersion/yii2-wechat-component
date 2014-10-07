<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/9/29
 * Time: 15:06
 */

namespace iit\wechat;


class MassNews extends News
{
    const SHOW_COVER_PIC = 1;
    const HIDE_COVER_PIC = 0;

    public function add($thumbMediaId, $title, $content, $digest = null, $author = null, $contentSourceUrl = null, $showCoverPic = null)
    {
        if ($this->count() <= self::MAX) {
            $this->_news[] = [
                'title' => $title,
                'thumb_media_id' => $thumbMediaId,
                'content' => $content,
                'digest' => $digest,
                'author' => $author,
                'content_source_url' => $contentSourceUrl,
                'show_cover_pic' => $showCoverPic
            ];
            return $this;
        } else {
            return false;
        }
    }

} 