<?php
/* @var $modx modX */

/**
 * Gets options from a standard MODX option string in a chunk and returns them as an array, e.g.,
 * Option1==Option One||Option2==Option Two
 * @param $tpl string - Options string from Tpl
 * @return array
 */
function parseOptions($chunk) {
    $options = array();

    if (!empty($chunk)) {
        $tempArray = explode('||', $chunk);
        foreach ($tempArray as $option) {
            $couple = explode('==', $option);
            $options[trim($couple[0])] = trim($couple['1']);
        }
    }
    return $options;
}

$output = '';
$props =& $scriptProperties;

$output .= '<h3>Total Active Users: ' . $modx->getCount('modUser', array('active' => '1')) . '</h3>';

$users = $modx->getIterator('modUser', array('active' => '1'));


$showInterests = $modx->getOption('sbs_show_interests');
$showGroups = $modx->getOption('sbs_show_groups');


if ($showInterests) {
    $chunkName = $modx->getOption('prefListTpl', $props, 'sbsPrefListTpl');
    $tpl = $modx->getChunk($chunkName);
    $possibleInterests = array();
    if (empty($tpl)) {
        return 'No Interest list chunk';
    } else {
        $possibleInterests = parseOptions($tpl);
    }

    $useCommentField = $modx->getOption('sbs_use_comment_field', $props, true);
    $extendedField = $modx->getOption('sbs_extended_field', $props, 'interests');
    $output .= '<h3>User Interests</h3>';
    $results = array();
    foreach ($possibleInterests as $caption => $key) {
        $results[$caption] = 0;
    }

    foreach ($users as $user) {
        /* @var $user modUser */
        $profile = $user->getOne('Profile');
        if ($useCommentField) {
            $interests = $profile->get('comment');
        } else {
            $extended = $profile->get('extended');
            $interests = $extended[$extendedField];
        }

        if (!empty($interests)) {

            $userInterests = explode(',', $interests);
            foreach($possibleInterests as $caption => $key) {
                if (in_array($key, $userInterests)) {
                    $results[$caption] = $results[$caption] + 1;
                }
            }
        }
    }
    $output .= "\n" . '<table border="2" cellpadding="10px" bgcolor="#eeeeee">' . "\n";
    $output .= "\n    <tr><th>Topic</th><th>Users</th></tr>";
    foreach($results as $cap => $total) {
        $output .= "\n    <tr><td>$cap</td><td>$total</td></tr>";

    }
    $output .= "\n</table>\n";


}

if ($showGroups) {
    reset($users);
    $chunkName = $modx->getOption('groupListTpl', $props, 'sbsGroupListTpl');
    $tpl = $modx->getChunk($chunkName);
    $possibleGroups = array();
    if (empty($tpl)) {
        $possibleGroups['Subscribers'] = 'Subscribers' ;
    } else {
        $possibleGroups = parseOptions($tpl);
    }
    $results = array();
    foreach ($possibleGroups as $caption => $key) {
        $results[$caption] = 0;
    }
    $output .= '<br /><br /><h3>User Groups</h3>';

    foreach($users as $user) {
        foreach ($possibleGroups as $caption => $key) {
            if ($user->isMember($key)) {
                $results[$caption] = $results[$caption] + 1;
            }
        }
    }
    $output .= "\n" . '<table border="2" cellpadding="10px" bgcolor="#eeeeee">' . "\n";
    $output .= "\n    <tr><th>Group</th><th>Users</th></tr>";
    foreach ($results as $cap => $total) {
        $output .= "\n    <tr><td>$cap</td><td>$total</td></tr>";

    }
    $output .= "\n</table>\n";

    $output .= '<br /><br />';


}

return $output;