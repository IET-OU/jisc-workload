<?php
/**
* CONFIGURATION - copy, then search and replace the text `{ EDIT ME }`
*
* Jisc / OU Student Workload Tool.
*
* @license   http://gnu.org/licenses/gpl.html GPL-3.0+
* @author    Jitse van Ameijde <djitsz@yahoo.com>
* @copyright 2015 The Open University.
*/

    return array(
        'tableSpecifications' => array(
            'sessions'=>array(
                'fields'=>array(
                    'sessionId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'userId'=>array('type'=>'int(11)', 'null'=>true, 'default'=>null),
                    'visitorId'=>array('type'=>'int(11)', 'null'=>false),
                    'ip'=>array('type'=>'varchar(40)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'referrer'=>array('type'=>'varchar(128)', 'collate'=>'utf8_unicode_ci', 'null'=>true),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastUpdated'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                ),
                'keys'=>array(
                    'primary'=>'sessionId',
                    'foreign'=>array(
                        'userId'=>array('references'=>'users.userId', 'on delete'=>'set null', 'on update'=>'cascade'),
                        'visitorId'=>array('references'=>'visitors.visitorId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                    )
                )
            ),
            'page_hits'=>array(
                'fields'=>array(
                    'pageHitId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'sessionId'=>array('type'=>'int(11)', 'null'=>true, 'default'=>null),
                    'uri'=>array('type'=>'varchar(64)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'errors'=>array('type'=>'int(11)', 'null'=>false),
                    'responseTime'=>array('type'=>'int(11)', 'null'=>false),
                    'dbHits'=>array('type'=>'int(11)', 'null'=>false),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00')
                ),
                'keys'=>array(
                    'primary'=>'pageHitId',
                    'foreign'=>array(
                        'sessionId'=>array('references'=>'sessions.sessionId', 'on delete'=>'set null', 'on update'=>'cascade')
                    )
                )
            ),
            'institutions'=>array(
                'fields'=>array(
                    'institutionId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'name'=>array('type'=>'varchar(64)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastUpdated'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'deleted'=>array('type'=>'datetime', 'null'=>true, 'default'=>null)
                ),
                'keys'=>array(
                    'primary'=>'institutionId'
                )
            ),
            'users'=>array(
                'fields'=>array(
                    'userId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'institutionId'=>array('type'=>'int(11)','null'=>false),
                    'firstName'=>array('type'=>'varchar(32)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'lastName'=>array('type'=>'varchar(64)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'email'=>array('type'=>'varchar(64)', 'collate'=>'utf8_unicode_ci', 'null'=>false, 'unique'=>true),
                    'login'=>array('type'=>'varchar(32)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'password'=>array('type'=>'char(40)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'resetToken'=>array('type'=>'char(40)', 'collate'=>'utf8_unicode_ci', 'null'=>true),
                    'accessLevel'=>array('type'=>'int(11)','null'=>false),
                    'status'=>array('type'=>'tinyint(3)', 'null'=>false, 'default'=>1),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastUpdatedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'lastUpdated'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastLogin'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'deletedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'deleted'=>array('type'=>'datetime', 'null'=>true, 'default'=>null)
                ),
                'keys'=>array(
                    'primary'=>'userId',
                    'foreign'=>array(
                        'lastUpdatedBy'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'deletedBy'=>array('references'=>'users.userId', 'on delete'=>'set null', 'on update'=>'cascade')
                    )
                )
            ),
            'visitors'=>array(
                'fields'=>array(
                    'visitorId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'acceptedCookies'=>array('type'=>'tinyint(1)', 'null'=>false, 'default'=>0),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastUpdated'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00')
                ),
                'keys'=>array(
                    'primary'=>'visitorId'
                )
            ),
            'courses'=>array(
                'fields'=>array(
                    'courseId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'code'=>array('type'=>'varchar(32)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'title'=>array('type'=>'varchar(64)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'presentation'=>array('type'=>'varchar(16)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'status'=>array('type'=>'tinyint(3)', 'null'=>false, 'default'=>1),
                    'units'=>array('type'=>'tinyint(3)', 'null'=>false, 'default'=>0),
                    'facultyId'=>array('type'=>'int(11)','null'=>false),
                    'level'=>array('type'=>'int(11)','null'=>false, 'default'=>0),
                    'credits'=>array('type'=>'int(11)','null'=>false, 'default'=>0),
                    'defaultWpm'=>array('type'=>'tinyint(3)', 'null'=>false, 'default'=>1),
                    'wpmLow'=>array('type'=>'int(11)','null'=>false),
                    'wpmMed'=>array('type'=>'int(11)','null'=>false),
                    'wpmHi'=>array('type'=>'int(11)','null'=>false),
                    'createdBy'=>array('type'=>'int(11)', 'null'=>false),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastUpdatedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'lastUpdated'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'deletedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'deleted'=>array('type'=>'datetime', 'null'=>true, 'default'=>null)
                ),
                'keys'=>array(
                    'primary'=>'courseId',
                    'foreign'=>array(
                        'facultyId'=>array('references'=>'faculties.facultyId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'createdBy'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'lastUpdatedBy'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'deletedBy'=>array('references'=>'users.userId', 'on delete'=>'set null', 'on update'=>'cascade')
                    )
                )
            ),
            'faculties'=>array(
                'fields'=>array(
                    'facultyId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'institutionId'=>array('type'=>'int(11)','null'=>false),
                    'name'=>array('type'=>'varchar(64)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'createdBy'=>array('type'=>'int(11)', 'null'=>false),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastUpdatedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'lastUpdated'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'deletedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'deleted'=>array('type'=>'datetime', 'null'=>true, 'default'=>null)
                ),
                'keys'=>array(
                    'primary'=>'facultyId',
                    'foreign'=>array(
                        'institutionId'=>array('references'=>'institutions.institutionId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'createdBy'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'lastUpdatedBy'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'deletedBy'=>array('references'=>'users.userId', 'on delete'=>'set null', 'on update'=>'cascade')
                    )
                )
            ),
            'collaborators'=>array(
                'fields'=>array(
                    'userId'=>array('type'=>'int(11)', 'null'=>false),
                    'courseId'=>array('type'=>'int(11)', 'null'=>false)
                ),
                'keys'=>array(
                    'primary'=>'courseId,userId',
                    'foreign'=>array(
                        'userId'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'courseId'=>array('references'=>'courses.courseId', 'on delete'=>'cascade', 'on update'=>'cascade')
                    )
                )
            ),
            'items'=>array(
                'fields'=>array(
                    'itemId'=>array('type'=>'int(11)', 'null'=>false, 'auto_increment'=>true),
                    'courseId'=>array('type'=>'int(11)', 'null'=>false),
                    'order'=>array('type'=>'int(11)', 'null'=>false),
                    'unit'=>array('type'=>'int(11)', 'null'=>false),
                    'title'=>array('type'=>'varchar(64)', 'collate'=>'utf8_unicode_ci', 'null'=>false),
                    'wordcount'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'wpm'=>array('type'=>'tinyint(3)', 'null'=>false,'default'=>1),
                    'av'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'other'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'FHI'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'communication'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'productive'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'experiential'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'interactive'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'assessment'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'tuition'=>array('type'=>'int(11)', 'null'=>true,'default'=>null),
                    'createdBy'=>array('type'=>'int(11)', 'null'=>false),
                    'created'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'lastUpdatedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'lastUpdated'=>array('type'=>'datetime', 'null'=>false, 'default'=>'0000-00-00 00:00:00'),
                    'deletedBy'=>array('type'=>'int(11)', 'null'=>false),
                    'deleted'=>array('type'=>'datetime', 'null'=>true, 'default'=>null)
                ),
                'keys'=>array(
                    'primary'=>'itemId',
                    'foreign'=>array(
                        'courseId'=>array('references'=>'courses.courseId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'createdBy'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'lastUpdatedBy'=>array('references'=>'users.userId', 'on delete'=>'cascade', 'on update'=>'cascade'),
                        'deletedBy'=>array('references'=>'users.userId', 'on delete'=>'set null', 'on update'=>'cascade')
                    )
                )
            )
        ),

        // EDIT ME - edit the `db`, `mailer` and `webroot` entries.

        'db' => array(
            'connectionString' => 'mysql:dbname={ EDIT ME };host={ EDIT ME };charset=utf8',
            // With PORT: 'connectionString' => 'mysql:dbname={ EDIT ME };host={ EDIT ME };port={ EDIT ME };charset=utf8',
            'username' => '{ EDIT ME }',
            'password' => '{ EDIT ME }'
        ),
        'mailer' => array(
            'default' => array(
                'host' => '{ EDIT ME }',
                'port' => 25,
                'username' => '{ EDIT ME }',
                'password' => '{ EDIT ME }',
            ),
        ),
        'webroot' => '',  //{ EDIT ME }
        'googleAnalyticsId' => 'UA-3845152-20',
    );

#End.
