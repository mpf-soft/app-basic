<?php

/* 
 * @author Mirel Nicu Mitache <mirel.mitache@gmail.com>
 * @package MPF Framework
 * @link    http://www.mpfframework.com
 * @category core package
 * @version 1.0
 * @since MPF Framework Version 1.0
 * @copyright Copyright &copy; 2011 Mirel Mitache 
 * @license  http://www.mpfframework.com/licence
 * 
 * This file is part of MPF Framework.
 *
 * MPF Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MPF Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MPF Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Multiple actions, controllers or rights can be selected on a single line.
 * Example
 *  return array(
 *      'users, cars, buildings' => array(
 *          'index, details' => '*',
 *          'add, delete, edit' => 'ADMIN, SUPERVISOR',
 *          '*' => '@'
 *      )
 *  )
 *
 *  Key symbols for rights:
 *       *  means that anyone has access
 *       @  means that all loggedin users have access
 *
 *  * used for actions means that that will be the default right for all actions
 *  that don't have rights selected from that specific controller(s)
 *
 *  moduleName/* used for controllers means that the rights will be applied as
 *  default for all controllers from that module that are not specified in the list
 *
 *  * used for controllers means that all controllers will have those default
 *  rights
 */
return array(
    'home' => array(
        '*' => '*'
    ),
    'user' => array(
        '*' => '@',
        'register, login, forgotpassword, resetpassword' => '*'
    ),
    'admin/user' => array(
        '*' => '@',
        'register, login, forgotpassword, resetpassword' => '*'
    ),
    'admin/*' => array(
        '*' => '@'
    )
);