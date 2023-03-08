import readline
import pathlib
import os
import shutil
import random
import string

def replace_file_text(file, text, replace_text):
    """Replaces a string in a file with another string.

    Args:
        file (str): file path
        text (str): original text
        text (str): replace text
    """
    with open(file, "r+") as f:
        data = f.read()
        data = data.replace(text, replace_text)
        f.seek(0)
        f.write(data)
        f.truncate()

def get_current_dir():
    """Returns current working directory."""
    return os.getcwd()

def get_script_dir():
    """Returns current working directory."""
    return os.path.dirname(os.path.realpath(__file__))

def read_string_input(*, init_value="", msg="input text"):
    """Reads a string from user input.

    Args:
        init_value (str, optional): [description]. Defaults to "".
        msg (str, optional): [description]. Defaults to "input text".

    Returns:
        [type]: [description]
    """
    # must be defined within function
    def prefill_hook():
        readline.insert_text(init_value)
        readline.redisplay()

    readline.set_pre_input_hook(prefill_hook)
    result = input(f"{msg}: ")
    readline.set_pre_input_hook()
    return result

def copy_to(src_path, dst_path, follow_symlinks=True, ignore_list=None):
    """Copies src_path to dst_path.
    If dst is a directory, a file with the same basename as src is created
    (or overwritten) in the directory specified.
    ignore_pattern:
        shutil ignore pattern (see doc, e.g. ignore_list = ['*.pyc', 'tmp*'])
    """
    try:
        if os.path.isdir(src_path):
            # copy dir recursively
            if ignore_list and len(ignore_list) > 0:
                shutil.copytree(
                    src_path,
                    dst_path,
                    symlinks=follow_symlinks,
                    ignore=shutil.ignore_patterns(*ignore_list),
                )
            else:
                shutil.copytree(src_path, dst_path, symlinks=follow_symlinks)
        else:
            # copy file
            shutil.copy(src_path, dst_path, follow_symlinks=follow_symlinks)
        return True
    except IOError as e:
        print(f"Unable to copy file. {e}")
        return False


def get_file_name(file_path, full=False):
    """Get file name of file"""
    if full:
        return to_path(file_path, as_string=False).name
    else:
        return to_path(file_path, as_string=False).stem


def to_path(*p, as_string=True):
    """Convert string to pathlib path.
    INFO: Path resolving removes stuff like ".." with 'strict=False' the
    path is resolved as far as possible -- any remainder is appended
    without checking whether it really exists.
    """
    pl_path = pathlib.Path(*p)
    ret = pl_path.resolve(strict=False)  # default return in case it is absolute path

    if not pl_path.is_absolute():
        # don't resolve relative paths (pathlib makes them absolute otherwise)
        ret = pl_path

    if as_string:
        return ret.as_posix()
    else:
        return ret

def join_paths(path, *paths, as_string=True):
    """Joins path with arbitrary amount of other paths."""
    joined = to_path(path, as_string=False).joinpath(to_path(*paths, as_string=False))
    joined_resolved = to_path(joined, as_string=False)
    if as_string:
        return joined_resolved.as_posix()
    else:
        return joined_resolved

def get_random_string(length):
    # choose from all lowercase letter
    letters = string.ascii_lowercase
    result_str = ''.join(random.choice(letters) for i in range(length))
    # print("Random string of length", length, "is:", result_str)
    return result_str