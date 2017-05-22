# Excess-128-converter
PHP Class to convert an arbitary floating point number to compressed Excess-128 format

The Excess-128 format is a biased representation of a floating point number. The compressed Excess-128 format uses 5 bytes to represent a floating point value: 1 byte for the exponent, 1 bit for the sign and 31 bits for the mantissa, which is left-truncated (the leading bit, that is always 1, is shifted off).

The Excess-128 format is used in Commodore BASIC V2, the C64's built-in interpreter. Read more at https://www.c64-wiki.com/wiki/Floating_point_arithmetic

This class provides a way to convert a float to Excess-128 format, you can use it in cross-assemblers, for educational purpose or just for fun.
