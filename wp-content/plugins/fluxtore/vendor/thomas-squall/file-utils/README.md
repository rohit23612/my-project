# File Utils for php

## List of available functions

1) [require_all_files](#require_all_files)
2) [require_once_all_files](#require_once_all_files)
3) [include_all_files](#include_all_files)
4) [include_once_all_files](#include_once_all_files)
5) [get_all_files](#get_all_files)
6) [get_all_dirs](#get_all_dirs)
7) [file_get_json](#file_get_json)
7) [file_put_json](#file_put_json)

### require_all_files

#### Description

Requires all the files in a folder that matches the provided pattern.

#### Definition

require_all_files($dir, $pattern = "*.php", $callback = null)

Where:
1) $dir is the directory where the files are located
2) $pattern is the pattern used to find the files (*.php by default)
3) $recursive will fetch also the child folders if set to true (default is false)
4) $callback is the callback called per each file (null by default, the file path will be passed as parameter)

#### Usage

``` php
function callback($file) {
    echo "file $file has been loaded..." . EOF;
}

require_all_fields("my/dir", "*.php", "callback");
```

### require_once_all_files

#### Description

Requires once all the files in a folder that matches the provided pattern.

#### Definition

require_once_all_files($dir, $pattern = "*.php", $callback = null)

Where:
1) $dir is the directory where the files are located
2) $pattern is the pattern used to find the files (*.php by default)
3) $recursive will fetch also the child folders if set to true (default is false)
4) $callback is the callback called per each file (null by default, the file path will be passed as parameter)

#### Usage

``` php
function callback($file) {
    echo "file $file has been loaded..." . EOF;
}

require_once_all_fields("my/dir", "*.php", "callback");
```

### include_all_files

#### Description

Includes all the files in a folder that matches the provided pattern.

#### Definition

include_all_files($dir, $pattern = "*.php", $callback = null)

Where:
1) $dir is the directory where the files are located
2) $pattern is the pattern used to find the files (*.php by default)
3) $recursive will fetch also the child folders if set to true (default is false)
4) $callback is the callback called per each file (null by default, the file path will be passed as parameter)

#### Usage

``` php
function callback($file) {
    echo "file $file has been loaded..." . EOF;
}

include_all_fields("my/dir", "*.php", "callback");
```

### include_once_all_files

#### Description

Includes once all the files in a folder that matches the provided pattern.

#### Definition

include_once_all_files($dir, $pattern = "*.php", $callback = null)

Where:
1) $dir is the directory where the files are located
2) $pattern is the pattern used to find the files (*.php by default)
3) $recursive will fetch also the child folders if set to true (default is false)
4) $callback is the callback called per each file (null by default, the file path will be passed as parameter)

#### Usage

``` php
function callback($file) {
    echo "file $file has been loaded..." . EOF;
}

include_once_all_files("my/dir", "*.php", "callback");
```

### get_all_files

#### Description

Get all the files in a folder that matches the provided pattern.

#### Definition

get_all_files($dir, $pattern = "*", $recursive = false)

Where:
1) $dir is the directory where the files are located
2) $pattern is the pattern used to find the files (*.php by default)
3) $recursive will fetch also the children folders if set to true (default is false)

#### Usage

``` php
print_r(get_all_files("my/dir"));
```

### get_all_dirs

#### Description

Returns all the directories in a given directory.

#### Definition

get_all_dirs($dir, $recursive = false)

Where:
1) $dir is the directory where the files are located
2) $recursive will fetch also the children directories if set to true (default is false)

#### Usage

``` php
print_r(get_all_files("my/dir"));
```

### file_get_json

#### Description

Reads the content of a json file and returns it as array or object.
Please Note: if the content is not a json encoded string an empty array will be returned.
 
#### Definition

file_get_json($file, $associative = true)

Where:
1) $file is the path to the file that we want to read
2) $associative will return the content as associative array when true, as object when false

#### Usage

``` php
$my_array = file_get_json("my/dir/my_file.json");
```

### file_put_json

#### Description

Saves an array or an object into a json file.
 
#### Definition

file_put_json($file, $content, $pretty = true)

Where:
1) $file is the path to the file that we want to save the content to
2) $content is the content we want to save (array or object)
3) $pretty will save the content prettified when true, minified when false

#### Usage

``` php
$my_array = [
    'fruit_1' => 'apple',
    'fruit_2' => 'pear',
    'fruit_3' => 'orange' 
];

file_put_json("my/dir/my_file.json", $my_array);
```

## More utilities coming...
