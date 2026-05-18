<?php

namespace CloudGamersDiscuss\API;

class Router {
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
    }

    public function registerRoutes() {
        $controllers = [
            new CommentController(),
            new ReactionController(),
            new RatingController(),
            new MailchimpController(),
        ];

        foreach ( $controllers as $controller ) {
            $controller->registerRoutes();
        }
    }
}
