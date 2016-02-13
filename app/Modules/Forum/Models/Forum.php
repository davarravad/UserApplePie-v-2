<?php

// Forum Model Class Database Goods

namespace Modules\Forum\Models;

use Core\Model;

class Forum extends Model {

// Function to get list of all enabled forum categories
  public function forum_categories(){
    $data = $this->db->select("
        SELECT
          *
        FROM
          ".PREFIX."forum_cat
        WHERE
          forum_name = 'forum'
        GROUP BY
          forum_title
        ORDER BY
          forum_order_title
        ASC
        ");
    return $data;
  }

  // Function to get list of all enabled forum categories
    public function forum_titles(){
      $data = $this->db->select("
          SELECT
            a.*,
            COUNT(DISTINCT t.forum_post_id) AS total_topics_display,
            COUNT(DISTINCT r.id) AS total_topic_replys_display
          FROM
            ".PREFIX."forum_cat AS a
          LEFT OUTER JOIN
            ".PREFIX."forum_posts AS t
            ON a.forum_id = t.forum_id
          LEFT OUTER JOIN
            ".PREFIX."forum_posts_replys AS r
            ON a.forum_id = r.fpr_id
          WHERE
            a.forum_name = 'forum'
          GROUP BY
            a.forum_id
          ORDER BY
            a.forum_order_cat
          ASC
          ");
      return $data;
    }

    // Function to get list of newest recent forum posts and replys.
    public function forum_recent_posts(){
      $data = $this->db->select("
        SELECT sub.*
        FROM
        (SELECT
          fp.forum_post_id as forum_post_id, fp.forum_id as forum_id,
          fp.forum_user_id as forum_user_id, fp.forum_title as forum_title,
          fp.forum_content as forum_content, fp.forum_edit_date as forum_edit_date,
          fp.forum_timestamp as forum_timestamp, fpr.id as id,
          fpr.fpr_post_id as fpr_post_id, fpr.fpr_id as fpr_id,
          fpr.fpr_user_id as fpr_user_id, fpr.fpr_title as fpr_title,
          fpr.fpr_content as fpr_content, fpr.fpr_edit_date as fpr_edit_date,
          fpr.fpr_timestamp as fpr_timestamp,
          GREATEST(fp.forum_timestamp, COALESCE(fpr.fpr_timestamp, '00-00-00 00:00:00')) AS tstamp
          FROM ".PREFIX."forum_posts fp
          LEFT JOIN ".PREFIX."forum_posts_replys fpr
          ON fp.forum_post_id = fpr.fpr_post_id
          ORDER BY tstamp DESC
        ) sub

        GROUP BY forum_post_id
        ORDER BY tstamp DESC
        LIMIT 10
      ");
      return $data;
    }

    // Function to get category data for current topic (Category)
    public function forum_cat($where_id){
      $data = $this->db->select("
        SELECT forum_cat
        FROM ".PREFIX."forum_cat
        WHERE forum_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_cat;
    }

    // Function to get category data for current topic (Description)
    public function forum_cat_des($where_id){
      $data = $this->db->select("
        SELECT forum_des
        FROM ".PREFIX."forum_cat
        WHERE forum_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_des;
    }

    // Function to get category data for current topic (Title)
    public function forum_title($where_id){
      $data = $this->db->select("
        SELECT forum_title
        FROM ".PREFIX."forum_cat
        WHERE forum_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_title;
    }

    // Function to get list of topics for given category
    public function forum_topics($where_id, $limit = null){
      $data = $this->db->select("
      SELECT
        sub.*,
        fpr_post_id,
        fpr_user_id AS LR_UserID,
        fpr_timestamp AS LR_TimeStamp,
        COUNT(fpr_post_id) AS total_topic_replys
      FROM
      (SELECT
        fp.forum_post_id as forum_post_id, fp.forum_id as forum_id,
        fp.forum_user_id as forum_user_id, fp.forum_title as forum_title,
        fp.forum_content as forum_content, fp.forum_edit_date as forum_edit_date,
        fp.forum_timestamp as forum_timestamp,
        fp.forum_status as forum_status, fpr.id as id,
        fpr.fpr_post_id as fpr_post_id, fpr.fpr_id as fpr_id,
        fpr.fpr_user_id as fpr_user_id, fpr.fpr_title as fpr_title,
        fpr.fpr_content as fpr_content, fpr.fpr_edit_date as fpr_edit_date,
        fpr.fpr_timestamp as fpr_timestamp,
        GREATEST(fp.forum_timestamp, COALESCE(fpr.fpr_timestamp, '00-00-00 00:00:00')) AS tstamp
        FROM ".PREFIX."forum_posts fp
        LEFT JOIN ".PREFIX."forum_posts_replys fpr
        ON fp.forum_post_id = fpr.fpr_post_id
        WHERE fp.forum_id = :where_id
        ORDER BY tstamp DESC
      ) sub
      GROUP BY forum_post_id
      ORDER BY tstamp DESC
      $limit
      ",
      array(':where_id' => $where_id));
      return $data;
    }

    // Function to get category ID that Topic is related to
    public function forum_topic_cat_id($where_id){
      $data = $this->db->select("
        SELECT forum_id
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_id;
    }

    // Function to get Topic Title for header
    public function topic_title($where_id){
      $data = $this->db->select("
        SELECT forum_title
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_title;
    }

    // Function to get Topic Creator userID
    public function topic_creator($where_id){
      $data = $this->db->select("
        SELECT forum_user_id
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_user_id;
    }

    // Function to get Topic Creator userID
    public function topic_date($where_id){
      $data = $this->db->select("
        SELECT forum_timestamp
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_timestamp;
    }

    // Function to get Topic Creator userID
    public function topic_content($where_id){
      $data = $this->db->select("
        SELECT forum_content
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_content;
    }

    // Function to get Topic Creator userID
    public function topic_edit_date($where_id){
      $data = $this->db->select("
        SELECT forum_edit_date
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_edit_date;
    }

    // Function to get Topic Creator userID
    public function topic_userID($where_id){
      $data = $this->db->select("
        SELECT forum_user_id
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_user_id;
    }

    // Function to get requested Topic Replys
    public function forum_topic_replys($where_id, $limit = null){
      $data = $this->db->select("
        SELECT
          *
        FROM
          ".PREFIX."forum_posts_replys
        WHERE
          fpr_post_id = :where_id
        ORDER BY
          id
        ASC
        $limit
      ",
      array(':where_id' => $where_id));
      return $data;
    }

    // Function to get total count of topic replys for topic
    public function getTotalReplys($where_id){
      $data = $this->db->select("
        SELECT
          *
        FROM
          ".PREFIX."forum_posts_replys
        WHERE
          fpr_post_id = :where_id
        ORDER BY
          id
        ASC
      ",
      array(':where_id' => $where_id));
      return count($data);
    }

    // Function to Create New Topic
    public function sendTopic($forum_user_id, $forum_id, $forum_title, $forum_content){
      // Format the Content for database
      $forum_content = nl2br($forum_content);
      // Update messages table
      $query = $this->db->insert(PREFIX.'forum_posts', array('forum_id' => $forum_id, 'forum_user_id' => $forum_user_id, 'forum_title' => $forum_title, 'forum_content' => $forum_content));
      $count = count($query);
      // Check to make sure Topic was Created
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }

    // Function to Edit Topic
    public function updateTopic($id, $forum_title, $forum_content){
      // Update messages table
      $query = $this->db->update(PREFIX.'forum_posts', array('forum_title' => $forum_title, 'forum_content' => $forum_content, 'forum_edit_date' => date('Y-m-d H:i:s')), array('forum_post_id' => $id));
      $count = count($query);
      // Check to make sure Topic was Created
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }

    // Function to get topic userID
    public function getTopicOwner($where_id){
      $data = $this->db->select("
        SELECT forum_user_id
        FROM ".PREFIX."forum_posts
        WHERE forum_post_id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->forum_user_id;
    }

    // Function to Create New Topic Reply
    public function sendTopicReply($fpr_user_id, $fpr_post_id, $fpr_id, $fpr_content){
      // Update messages table
      $query = $this->db->insert(PREFIX.'forum_posts_replys', array('fpr_post_id' => $fpr_post_id, 'fpr_user_id' => $fpr_user_id, 'fpr_id' => $fpr_id, 'fpr_content' => $fpr_content));
      $count = count($query);
      // Check to make sure Topic was Created
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }

    // Function to get last topic reply inserted ID
    public function lastTopicReplyID($where_id){
      $data = $this->db->select("
        SELECT id
        FROM ".PREFIX."forum_posts_replys
        WHERE fpr_post_id = :where_id
        ORDER BY id DESC
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->id;
    }

    // Function to Edit Topic Reply
    public function updateTopicReply($id, $fpr_content){
      // Update messages table
      $query = $this->db->update(PREFIX.'forum_posts_replys', array('fpr_content' => $fpr_content, 'fpr_edit_date' => date('Y-m-d H:i:s')), array('id' => $id));
      $count = count($query);
      // Check to make sure Topic was Created
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }

    // Function to get topic reply userID
    public function getReplyOwner($where_id){
      $data = $this->db->select("
        SELECT fpr_user_id
        FROM ".PREFIX."forum_posts_replys
        WHERE id = :where_id
        LIMIT 1
      ",
      array(':where_id' => $where_id));
      return $data[0]->fpr_user_id;
    }

}
