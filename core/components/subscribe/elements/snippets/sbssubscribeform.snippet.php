<?php
/**
 * Subscribe
 * Copyright 2012 Bob Ray <http://bobsguides/com>
 *
 * Subscribe is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * Subscribe is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Subscribe; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package subscribe
 * @author Bob Ray <http://bobsguides/com>
 *
 * @version Version 1.0.0 Beta-1
 * 3/3/12
 *
 * @Description Display request to subscribe unless user is admin or logged in */


/**
 * @version Version 1.0.0 Beta-1
 * @package subscribe
 */


/* Display request to subscribe unless user is admin or logged in */
/* @var $modx modX */
/* @var $scriptProperties array */

/* Properties
 *
 * &confirmRegisterPageId int (required) ID of ConfirmRegister resource
 *     default: empty
 *
 * &thankYouPageId int (required) ID of Thank You for Registering page
 *     default: empty
 *
 * &cssPath string
 *      default: MODX_ASSETS_PATH .components/subscribe/css/
 *
 *  &cssFile string
 *      default: subscribe.css
 *
 *  &activationEmailTpl string Name of activation email Tpl chunk
 *      default: activatEmailTpl
 *
 * &whyDialogTpl string Tpl chunk for Why Subscribe dialog
 *      default: sbsWhyDialogTpl
 *
 * &whyDialogTextTpl string Tpl chunk for Why dialog text
 *      default: sbsWhyDialogTextTpl
 *
 * &privacyDialogTpl string Tpl chunk for Privacy dialog
 *      default: sbsPrivacyDialogTpl
 *
 * &privacyDialogTextTpl string Tpl chunk for Privacy dialog text
 *      default: sbsPrivacyDialogTextTpl
 * 
 *  &language string language to use for buttons and messages
 *      default: en
 *
 * */


$sp =& $scriptProperties;
//return 'test';
/* don't allow manage preferences if user is not logged in */
if ($sp['form'] == 'managePrefs') {
    if (! $modx->user->hasSessionContext($modx->context->get('key'))) {
        $modx->sendUnauthorizedPage();
    }
}

/* load CSS file unless &cssPath or &cssFile is set to 'none' */
if ($sp['cssPath'] == 'none' || $sp['cssFile'] == 'none') {
    $css = false;
} else {
    $cssPath = $modx->getOption('cssPath', $sp, null);
    $cssPath = empty($cssPath)
        ? MODX_ASSETS_URL . 'components/subscribe/css/'
        : $cssPath;

    $cssFile = $modx->getOption('cssFile', $sp, null);
    $cssFile = empty($cssFile)
        ? 'subscribe.css'
        : $cssFile;

    $css = $cssPath . $cssFile;
}

if ($css) {
    $modx->regClientCSS($css);
}

/* load language strings */
$language = !empty($scriptProperties['language'])
    ? $scriptProperties['language']
    : $modx->getOption('cultureKey', null, $modx->getOption('manager_language', null, 'en'));
$language = empty($language) ? 'en' : $language;
$modx->lexicon->load($language . ':subscribe:forms');

$s = $modx->lexicon->fetch($prefix = 'sbs_js_',$removePrefix = false);
$sj = $modx->toJSON($s);
$modx->setPlaceholder('sbs_lexicon_json',$sj);

/* load JS file */
$jsPath = $modx->getOption('jsPath', $sp, null);
$jsPath = empty($jsPath)
    ? MODX_ASSETS_URL . 'components/subscribe/js/'
    : $jsPath;

$jsFile = $modx->getOption('jsFile', $sp, null);
$jsFile = empty($jsFile)
    ? 'subscribe.js'
    : $jsFile;

$modx->regClientStartupScript($jsPath . $jsFile);


$interestListTpl = $modx->getOption('interestListTpl', $sp, 'sbsInterestListTpl');
$intString = $modx->getChunk($interestListTpl);

/* turn ints into an associative array */
$ints = explode('||',$intString);
$checkboxTpl = $modx->getChunk('sbsCheckboxTpl');
$intsPh = '';
foreach($ints as $s) {
    $couple = explode ('==', $s);
    $result[trim($couple[0])] = trim($couple[1]);
    $line = str_replace('[[+sbs_value]]', trim($couple[0]),$checkboxTpl);
    $line = str_replace('[[+sbs_caption]]', trim($couple[1]),$line);
    $intsPh .= $line;
}

if ($sp['form'] == 'managePrefs') {
    $profile = $modx->user->getOne('Profile');
    $modx->setPlaceholder('sbs_username', $modx->user->get('username'));

    /* show current preferences unless posted from managaPrefs form
     * (in which case the RecordPreferences snippet will set them).
    */
    if ($profile && (!isset($_POST['sbs_manage_prefs_form']) || isset($_POST['unsubscribe']))) {
        $prefs = $profile->get('comment');
        $modx->setPlaceholder('sbs_current_prefs', $prefs);
    }

    $output = $modx->getChunk('sbsManagePrefsFormTpl');
} else if ($sp['form'] == 'register') {
    $fields = array();
    $fields['confirmRegisterPageId'] = $sp['confirmRegisterPageId'];
    $fields['thankYouPageId'] = $sp['thankYouPageId'];
    $fields['activationEmailTpl'] = $modx->getOption('activationEmailTpl', $sp, 'sbsActivationEmailTpl');

    $output = $modx->getChunk('sbsRegisterFormTpl', $fields);
} else {
    $output = 'Unauthorized Access';
}
$output = str_replace('[[+sbs_interest_list]]', $intsPh, $output);
return $output;