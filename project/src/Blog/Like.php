<?php

namespace GeekBrains\Project\Blog;

class Like
{
    public function __construct(
        private UUID $uuid,
        private UUID $post_uuid,
        private UUID $user_uuid,
    ) {
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return UUID
     */
    public function postUuid(): UUID
    {
        return $this->post_uuid;
    }

    /**
     * @param UUID $post_id
     */
    public function setPostId(UUID $post_uuid): void
    {
        $this->post_uuid = $post_uuid;
    }

    /**
     * @return UUID
     */
    public function userUuid(): UUID
    {
        return $this->user_uuid;
    }

    /**
     * @param UUID $user_id
     */
    public function setUserId(UUID $user_uuid): void
    {
        $this->user_uuid = $user_uuid;
    }
}
