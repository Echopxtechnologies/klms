<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Elearning_admin_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_course($data)
    {
        $insert = [
        'title'           => $data['title'],
        'category'        => $data['category'],
        'description'     => $data['description'],
        'cover_image'     => isset($data['cover_image']) ? $data['cover_image'] : null,
        'price'           => isset($data['price']) ? (float)$data['price'] : 0.00,
        'is_free'         => isset($data['is_free']) ? 1 : 0,
        'is_public'       => isset($data['is_public']) ? 1 : 0,
        'is_active'       => isset($data['is_active']) ? 1 : 0,
        'course_duration' => isset($data['course_duration']) ? $data['course_duration'] : null,
        'level'           => isset($data['level']) ? $data['level'] : 'beginner',
        'language'        => isset($data['language']) ? $data['language'] : 'English',
        'sort_order'      => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
        'created_at'      => date('Y-m-d H:i:s'),
        'updated_at'      => date('Y-m-d H:i:s')
        ];
        $this->db->insert('elearning_courses', $insert);
        return $this->db->insert_id();
    }

    public function get_courses()
    {
        // "<?php echo admin_url('klms/Lms_admin/edit_course/'.$course['id']); "
        return $this->db->get('elearning_courses')->result_array();
    }

    public function update_course($id, $data)
{
    $update = [
        'title'           => $data['title'],
        'category'        => $data['category'],
        'description'     => $data['description'],
        'price'           => isset($data['price']) ? (float)$data['price'] : 0.00,
        'is_free'         => isset($data['is_free']) ? 1 : 0,
        'is_public'       => isset($data['is_public']) ? 1 : 0,
        'is_active'       => isset($data['is_active']) ? 1 : 0,
        'course_duration' => isset($data['course_duration']) ? $data['course_duration'] : null,
        'level'           => isset($data['level']) ? $data['level'] : 'beginner',
        'language'        => isset($data['language']) ? $data['language'] : 'English',
        'sort_order'      => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
        'updated_at'      => date('Y-m-d H:i:s')
    ];
    
    // Only update cover_image if provided
    if (isset($data['cover_image'])) {
        $update['cover_image'] = $data['cover_image'];
    }
    
    $this->db->where('id', $id);
    $this->db->update('elearning_courses', $update);
    return $this->db->affected_rows() > 0;
}

    
    public function add_video($data)
    {
        $insert = [
            'course_id'     => $data['course_id'],
            'title'         => $data['title'],
            'description'   => isset($data['description']) ? $data['description'] : '',
            'vimeo_url'     => $data['vimeo_url'],
            'sort_order'    => isset($data['sort_order']) ? $data['sort_order'] : 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tblelearning_videos',$insert);
        return $this->db->insert_id();
    }

    public function get_course($id)
    {
        $this->db->where('id',$id);
        return $this->db->get('elearning_courses')->row_array();
    }

    public function get_course_videos($course_id)
    {
        $this->db->where('course_id', $course_id);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'ASC');
        return $this->db->get('elearning_videos')->result_array();
    }
    
    public function get_video($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('elearning_videos')->row_array();
    }

    public function update_video($id,$data)
    {
        if (empty($id) && empty($data)){
            return null;
        }

        $update = [
            'title'       => $data['title'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'vimeo_url'   => $data['vimeo_url'],
            'sort_order'  => isset($data['sort_order']) ? $data['sort_order'] : 0,
            'duration'    => isset($data['duration']) ? $data['duration'] : '',
            'updated_at'  => date('Y-m-d H:i:s')
        ];
        $this->db->where('id',$id);
        $this->db->update('elearning_videos',$update);
        return $this->db->affected_row() > 0;
    }
    public function delete_video($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('elearning_videos');
        return $this->db->affected_rows() > 0;
    }
}
