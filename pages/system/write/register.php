<?php
if ($book->checkRegister() < 1) {
    if ($book->register()) echo 1;
    else echo -1;
} else { // unregister
    if ($book->unregister()) echo 2;
    else echo -2;
}
