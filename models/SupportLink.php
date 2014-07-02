<?php

/**
 * SupportLink.php
 * model class for table supportplugin_links
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @copyright   2014 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
class SupportLink extends SimpleORMap {

    public function __construct($id = null) {
        $this->db_table = 'supportplugin_links';
        parent::__construct($id);
    }

}
