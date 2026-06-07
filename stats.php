<?php
// =====================================================
// NexaFlow – API : Statistiques Dashboard
// =====================================================
require_once '../../includes/functions.php';
requireAuth();
$uid = $_SESSION['user_id'];

$out = [];

// Déterminer les filtres selon le rôle
$is_admin = ($_SESSION['user_role'] === 'admin');
$is_manager = ($_SESSION['user_role'] === 'chef_projet');

if ($is_admin) {
    $user_filter = "";
} elseif ($is_manager) {
    $user_filter = " AND (p.owner_id = $uid OR p.id IN (SELECT project_id FROM project_members WHERE user_id = $uid) OR p.team_id IN (SELECT team_id FROM team_members WHERE user_id = $uid))";
} else {
    $user_filter = " AND (p.owner_id = $uid OR p.id IN (SELECT project_id FROM project_members WHERE user_id = $uid))";
}

// Projets actifs (Tout sauf 'done')
$s = db()->prepare("SELECT COUNT(*) as c FROM projects p WHERE p.status IN ('active','late','planned') $user_filter");
$s->execute(); $out['projects_active'] = (int)$s->fetch()['c'];

// Tâches terminées (filtrées par projet accessibles si non admin)
$task_filter = $is_admin ? "" : " AND project_id IN (SELECT p.id FROM projects p WHERE 1=1 $user_filter)";
$s = db()->prepare("SELECT COUNT(*) as c FROM tasks WHERE status = 'done' $task_filter");
$s->execute(); $out['tasks_done'] = (int)$s->fetch()['c'];

// Tâches en cours
$s = db()->prepare("SELECT COUNT(*) as c FROM tasks WHERE status IN ('todo','in_progress','review') $task_filter");
$s->execute(); $out['tasks_progress'] = (int)$s->fetch()['c'];

// Tâches en retard
$s = db()->prepare("SELECT COUNT(*) as c FROM tasks WHERE status != 'done' AND due_date < CURDATE() $task_filter");
$s->execute(); $out['tasks_late'] = (int)$s->fetch()['c'];

// Membres actifs (tous les membres de l'organisation)
$s = db()->prepare("SELECT COUNT(*) as c FROM users WHERE is_active = 1");
$s->execute(); $out['members'] = (int)$s->fetch()['c'];

// Répartition projets
$s = db()->prepare("SELECT p.status, COUNT(*) as c FROM projects p WHERE 1=1 $user_filter GROUP BY p.status");
$s->execute(); $out['projects_by_status'] = $s->fetchAll();

// Total projets
$s = db()->prepare("SELECT COUNT(*) as c FROM projects p WHERE 1=1 $user_filter");
$s->execute(); $out['projects_total'] = (int)$s->fetch()['c'];

// Projets récents 
$s = db()->prepare("
    SELECT p.*, u.firstname, u.lastname,
           (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count,
           (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as task_count
    FROM projects p
    LEFT JOIN users u ON p.owner_id = u.id
    WHERE 1=1 $user_filter
    ORDER BY p.updated_at DESC LIMIT 3
");
$s->execute(); $out['recent_projects'] = $s->fetchAll();

// Tâches prioritaires
$s = db()->prepare("
    SELECT t.*, p.name as project_name, u.firstname, u.lastname, u.avatar_initials
    FROM tasks t
    LEFT JOIN projects p ON t.project_id = p.id
    LEFT JOIN users u ON t.assigned_to = u.id
    WHERE t.status != 'done' $task_filter
    ORDER BY FIELD(t.priority,'haute','moyenne','basse'), t.due_date ASC
    LIMIT 5
");
$s->execute(); $out['priority_tasks'] = $s->fetchAll();

// Activités récentes
$s = db()->prepare("
    SELECT a.*, u.firstname, u.lastname, u.avatar_initials
    FROM activities a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC LIMIT 10
");
$s->execute();
$activities = $s->fetchAll();
foreach ($activities as &$act) {
    $act['time_ago'] = timeAgo($act['created_at']);
}
$out['activities'] = $activities;

// Notifications non lues
$s = db()->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$s->execute([$uid]);
$notifs = $s->fetchAll();
foreach ($notifs as &$n) {
    $n['time_ago'] = timeAgo($n['created_at']);
}
$out['notifications'] = $notifs;

// Activité hebdomadaire (7 derniers jours)
$week = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $s = db()->prepare("SELECT COUNT(*) as c FROM tasks WHERE status = 'done' AND DATE(updated_at) = ? $task_filter");
    $s->execute([$date]);
    $week[] = ['date' => $date, 'day' => date('D', strtotime($date)), 'count' => (int)$s->fetch()['c']];
}
$out['weekly_activity'] = $week;

// Nombre total de messages (pour le badge)
$s = db()->prepare("SELECT COUNT(*) as c FROM messages");
$s->execute(); $out['messages_count'] = (int)$s->fetch()['c'];

jsonResponse($out);
