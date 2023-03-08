#!/usr/bin/env python3

###
# File: create_config.py
# Created: May 2022
# Author: Andreas (aleibets@itec.aau.at)
# -----
# Last Modified: December 2022
# Modified By: Andreas (aleibets@itec.aau.at)
# -----
# Copyright (c) 2022 Klagenfurt University
#
###

import argparse
import sys
import utils
import textwrap

# {input_template: output_folder}
TEMPLATE_MAPPING = {
    "./templates/prepare_db-1.2.sql": "../config/",
    "./templates/config.php": "../config/",
    "./templates/prepare_db.sh": "../config/",
    "./templates/dump_db.sh": "../config/",
    "./templates/restore_db.sh": "../config/",
    "./templates/db_upgrade-1.0-1.2.sql": "../config/",
}
TEMPLATE_DB_NAME = "DB_NAME"
TEMPLATE_DB_PASS = "DB_PASS"
TEMPLATE_DB_ADMIN = "DB_ADMIN"
DB_NAME_DEFAULT = "ontis_annotation_tool"
DB_ADMIN_DEFAULT = "root"


def main():

    print()
    print("Please enter/confirm the following:")

    # replace content with user input
    db_admin_input = utils.read_string_input(
        msg="database admin (ONLY used for DB setup)", init_value=DB_ADMIN_DEFAULT
    )
    db_name_input = utils.read_string_input(
        msg="database name/user", init_value=DB_NAME_DEFAULT
    )
    db_pass_input = utils.get_random_string(15)
    db_pass_input = utils.read_string_input(
        msg="database pass", init_value=db_pass_input
    )

    print()

    for input_template, output_folder in TEMPLATE_MAPPING.items():
        # copy template
        fn = utils.get_file_name(input_template, True)
        out_file = utils.join_paths(output_folder, fn)
        utils.copy_to(input_template, out_file)
        utils.replace_file_text(out_file, TEMPLATE_DB_ADMIN, db_admin_input)
        utils.replace_file_text(out_file, TEMPLATE_DB_NAME, db_name_input)
        utils.replace_file_text(out_file, TEMPLATE_DB_PASS, db_pass_input)
        print(f"Created file {out_file}")


if __name__ == "__main__":
    main()
