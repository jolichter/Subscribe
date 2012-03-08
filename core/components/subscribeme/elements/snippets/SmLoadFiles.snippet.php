<?php
/**
 * SubscribeMe
 * Copyright 2012 Bob Ray <http://bobsguides/com>
 *
 * SubscribeMe is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * SubscribeMe is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * SubscribeMe; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package subscribeme
 * @author Bob Ray <http://bobsguides/com>
 *
 * @version Version 1.0.0 Beta-1
 * 3/3/12
 *
 * Description loads SubscribeMe JS and CSS files if user is not logged in */


/**
 *  @version Version 1.0.0 Beta-1
 *  @package subscribeme
 */

/* @var $modx modX */

if ($modx->user->hasSessionContext($modx->context->get('key')) ) {
    return '';
}
return $modx->getChunk('SmLoadFiles');