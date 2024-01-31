<?php

# i'm a comment, i shouldn't be deleted

echo 'I\'m code, don\'t delete me';
echo 'code...';
echo 'code....';

# FEATURE_LARAVEL_PWA:START
echo 'FEATURE_LARAVEL_PWA1';
# FEATURE_LARAVEL_PWA:END

# FEATURE_LARAVEL_PWA:START
echo 'FEATURE_LARAVEL_PWA2';
# FEATURE_LARAVEL_PWA:END

# FEATURE_LARAVEL_PWA:START
echo 'FEATURE_LARAVEL_PWA3';
# FEATURE_LARAVEL_PWA:END

// should keep 3 and 4
# FEATURE_LARAVEL_PWA:START
echo 'FEATURE_LARAVEL_PWA3';
echo 'FEATURE_LARAVEL_PWA4';
# FEATURE_LARAVEL_PWA:END

# FEATURE_LARAVEL_PWA:START#######
echo 'FEATURE_LARAVEL_PWA5';
# FEATURE_LARAVEL_PWA:END########


# FEATURE_WHERE_TAG_DOES_NOT_EXIST:START
# My flags should be deleted regardless of the tag because they are not in the array
# FEATURE_WHERE_TAG_DOES_NOT_EXIST:END

echo 'end of file';
