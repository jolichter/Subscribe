<?php
/**
 * Inserts the javascript code to validate the form.
 *
 */

/* javascript validation */
/* load email check  JS */
/* @var $modx modX */
$inFile = $modx->getOption('assets_url') . "components/subscribe/js/emailcheck.js";
$modx->regClientStartupScript($inFile);

$inFile = $modx->getOption('assets_url') . "components/subscribe/css/subscribe.css";
$modx->regClientCSS($inFile);



if (!empty($scriptProperties['language'])) {
    $modx->setOption('cultureKey', $scriptProperties['language']);
}
$language = !empty($scriptProperties['language'])
    ? $scriptProperties['language']
    : $modx->getOption('cultureKey', null, $modx->getOption('manager_language', null, 'en'));
$language = empty($language) ? 'en' : $language;

$modx->lexicon->load($language . ':subscribe:default');
$fields = array();
$fields['sbs_username_required'] = $modx->lexicon('sbs_username_required');
$fields['sbs_username_too_short'] = $modx->lexicon('sbs_username_too_short');
$fields['sbs_password_required'] = $modx->lexicon('sbs_password_required');
$fields['sbs_password_too_short'] = $modx->lexicon('sbs_password_too_short');
$fields['sbs_password_mismatch'] = $modx->lexicon('sbs_password_mismatch');
$fields['sbs_email_required'] = $modx->lexicon('sbs_email_required');
$fields['sbs_bad_email'] = $modx->lexicon('sbs_bad_email');
$fields['sbs_fullname_required'] = $modx->lexicon('sbs_fullname_required');
$fields['sbs_interests_required'] = $modx->lexicon('sbs_interests_required');


$src = $modx->getChunk('SbsRegisterJsTpl', $fields);

$modx->regClientStartupScript($src);

return '';