<?php

namespace CloudGamersDiscuss\API;

class CommentController {
    public function registerRoutes() {
        register_rest_route( 'cgd/v1', '/comments', [
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [ $this, 'getComments' ],
                'permission_callback' => '__return_true',
            ],
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'createComment' ],
                'permission_callback' => '__return_true',
            ],
        ] );
    }

    public function getComments( $request ) {
        $post_id = $request->get_param( 'post' );
        $page    = $request->get_param( 'page' ) ?: 1;
        $sort    = $request->get_param( 'sort' ) ?: 'newest';
        $number  = 10;
        $offset  = ( $page - 1 ) * $number;

        $args = [
            'post_id' => $post_id,
            'number'  => $number,
            'offset'  => $offset,
            'status'  => 'approve',
            'order'   => ( $sort === 'oldest' ) ? 'ASC' : 'DESC',
            'orderby' => ( $sort === 'top' ) ? 'comment_karma' : 'comment_date',
        ];

        $comments = get_comments( $args );
        $formatted_comments = [];

        foreach ( $comments as $comment ) {
            $formatted_comments[] = $this->prepareCommentForResponse( $comment );
        }

        return [
            'comments' => $formatted_comments,
            'hasMore'  => count( $formatted_comments ) === $number,
        ];
    }

    public function createComment( $request ) {
        $params = $request->get_json_params();

        // Basic validation
        if ( empty( $params['comment'] ) || empty( $params['author'] ) ) {
            return new \WP_Error( 'missing_fields', 'Please fill in all required fields.', [ 'status' => 400 ] );
        }

        // Honeypot/Spam check (basic)
        if ( ! empty( $params['hp_field'] ) || \CloudGamersDiscuss\Utils\Security::isSpam( $params ) ) {
            return new \WP_Error( 'spam', 'Spam detected.', [ 'status' => 403 ] );
        }

        $content = \CloudGamersDiscuss\Utils\Security::filterProfanity( $params['comment'] );
        $content = \CloudGamersDiscuss\Utils\Security::parseContent( $content );

        $commentdata = [
            'comment_post_ID'      => $params['comment_post_ID'],
            'comment_author'       => sanitize_text_field( $params['author'] ),
            'comment_author_email' => sanitize_email( $params['email'] ?? '' ),
            'comment_content'      => wp_kses_post( $content ),
            'comment_type'         => 'comment',
            'comment_parent'       => intval( $params['comment_parent'] ?? 0 ),
            'user_id'              => 0, // Always guest for now as per requirements
            'comment_approved'     => 1, // Auto-approve for demo, should be filtered
        ];

        $comment_id = wp_insert_comment( $commentdata );

        if ( ! $comment_id ) {
            return new \WP_Error( 'db_error', 'Could not save comment.', [ 'status' => 500 ] );
        }

        // Handle Newsletter Subscription
        if ( ! empty( $params['subscribe'] ) && ! empty( $params['email'] ) ) {
            $mailchimp = new \CloudGamersDiscuss\Integrations\Mailchimp();
            $mailchimp->subscribe( $params['email'], $params['author'] );
        }

        $comment = get_comment( $comment_id );
        return $this->prepareCommentForResponse( $comment );
    }

    private function prepareCommentForResponse( $comment ) {
        return [
            'id'                  => $comment->comment_ID,
            'author_name'         => $comment->comment_author,
            'author_avatar_urls'  => [
                '48' => get_avatar_url( $comment->comment_author_email, [ 'size' => 48 ] ),
            ],
            'date_relative'       => human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'cloud-gamers-discuss' ),
            'content'             => [
                'rendered' => apply_filters( 'comment_text', $comment->comment_content, $comment ),
            ],
            'reactions'           => $this->getReactions( $comment->comment_ID ),
        ];
    }

    private function getReactions( $comment_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'cgd_reactions';
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT type, COUNT(*) as count FROM $table WHERE comment_id = %d GROUP BY type",
            $comment_id
        ) );

        $reactions = [];
        foreach ( $results as $row ) {
            $reactions[ $row->type ] = (int) $row->count;
        }

        return $reactions;
    }
}
