<?php
namespace ZenDump;

class Article extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'articles';
    public $primaryKey = 'article_id';
}

class ArticleAttachment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'article_attachments';
    public $primaryKey = 'attachment_id';
}

class Attachment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'attachments';
    public $primaryKey = 'attachment_id';
}

class Automation extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'automations';
    public $primaryKey = 'automation_id';
}

class AutomationAction extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'automation_actions';
}

class AutomationCondition extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'automation_conditions';
}

class Category extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'categories';
    public $primaryKey = 'category_id';
}

class Comment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'comments';
    public $primaryKey = 'comment_id';
}

class Error extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'errors';
}

class Group extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'groups';
    public $primaryKey = 'group_id';
}

class Label extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'article_labels';
    public $primaryKey = 'label_id';
}

class Macro extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'macros';
    public $primaryKey = 'macro_id';
}

class MacroAction extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'macro_actions';
}

class MacroRestriction extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'macro_restrictions';
}

class Membership extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'group_memberships';
    public $primaryKey = 'membership_id';
}

class Meta extends \Illuminate\database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'meta';
}

class Section extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'sections';
    public $primaryKey = 'section_id';
}

class Target extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = array(
        'target_id',
        'title',
        'created_at',
        'type',
        'active',
        'method',
        'attribute',
        'username',
        'password',
        'target_url',
        'url'
    );
    public $timestamps = false;
    public $table = 'targets';
    public $primaryKey = 'target_id';
}

class Ticket extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'tickets';
    public $primaryKey = 'ticket_id';
}

class TicketList extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'ticket_list';
}

class Trigger extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'triggers';
    public $primaryKey = 'trigger_id';
}

class TriggerAction extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'trigger_actions';
}

class TriggerCondition extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'trigger_conditions';
}

class User extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'users';
    public $primaryKey = 'user_id';
}
 ?>
