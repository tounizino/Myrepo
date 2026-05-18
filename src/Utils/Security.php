<?php

namespace CloudGamersDiscuss\Utils;

class Security {
    public static function filterProfanity( $content ) {
        $bad_words = get_option( 'cgd_profanity_list', 'fuck,shit,asshole,bitch' );
        $bad_words = explode( ',', $bad_words );
        $bad_words = array_map( 'trim', $bad_words );

        foreach ( $bad_words as $word ) {
            $pattern = '/\b' . preg_quote( $word, '/' ) . '\b/i';
            $content = preg_replace( $pattern, str_repeat( '*', strlen( $word ) ), $content );
        }

        return $content;
    }

    public static function isSpam( $comment_data ) {
        // Basic rate limiting
        $ip = $_SERVER['REMOTE_ADDR'];
        $last_comment_time = get_transient( 'cgd_last_comment_' . md5( $ip ) );

        if ( $last_comment_time && ( time() - $last_comment_time ) < 30 ) {
            return true; // 30 seconds rate limit
        }

        set_transient( 'cgd_last_comment_' . md5( $ip ), time(), 30 );

        return false;
    }

    public static function parseContent( $content ) {
        // Spoilers: [spoiler]text[/spoiler]
        $content = preg_replace( '/\[spoiler\](.*?)\[\/spoiler\]/is', '<span class="cgd-spoiler">$1</span>', $content );

        // Mentions: @username
        $content = preg_replace( '/@(\w+)/', '<span class="cgd-mention">@$1</span>', $content );

        return $content;
    }
}
