#define _GNU_SOURCE 1

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

/*
 * A simple program to XOR file
 */

#define KEY_LENGHT_MAX 255

unsigned char
_rotl(const unsigned char value, int shift)
{
    if ((shift &= sizeof(value) * 8 - 1) == 0)
        return value;
    return (value << shift) | (value >> (sizeof(value) * 8 - shift));
}

unsigned char
_rotr(const unsigned char value, int shift)
{
    if ((shift &= sizeof(value) * 8 - 1) == 0)
        return value;
    return (value >> shift) | (value << (sizeof(value) * 8 - shift));
}

int
main(int argc, char *argv[])
{
    FILE *in = stdin;
    FILE *out = stdout;

    char *key;
    char *newkey;
    unsigned int key_index = 0;
    unsigned int key_length = 0;
    int readed_byte;
    unsigned char read_byte;
    unsigned short int silent = 0;
    int c;
    //int i;

    while ((c = getopt(argc, argv, "i:o:k:hs")) != -1) {
        switch (c) {
        case 'i':
            if (in != stdin) {
                fclose(in);
            }
            in = fopen(optarg, "rb");
            break;
        case 'o':
            if (out != stdout) {
                fclose(out);
            }
            out = fopen(optarg, "wb");
            break;
        case 'k':
            key = strndup(optarg, KEY_LENGHT_MAX);
            key_length = strlen(optarg);
            if (key_length > KEY_LENGHT_MAX) {
                key_length = KEY_LENGHT_MAX;
                fprintf(stderr, "too long truncated\n");
            }
            break;
        case 's':
            // Need to be silent and extand key with Secret data
            silent = 1;
            break;
        case 'h':
            /* fprintf(stderr, "usage: %s -k key [-i input] [-o output]\n",
             * argv[0]); */
            return EXIT_SUCCESS;
        }
    }

    if (key == NULL) {
        fprintf(stderr, "WTF ?\n");
        return EXIT_FAILURE;
    }

    if (silent == 1) {
        // Extend key ...
        char *oldkey;
        oldkey = (char *) malloc(KEY_LENGHT_MAX);
        if (key_length > 8) {
            key_length = 8;
        }
        strncpy(oldkey, key, key_length);
        newkey = malloc(key_length * 8);
        for (int i = 0; i < (key_length * 8); i++) {
            newkey[i] =
                (unsigned
                 char) ((oldkey[i % key_length] ^
                         (_rotr(oldkey[i % key_length], (i % 8)))) & 0xFF);
            //            fprintf(stderr, "%02X - %02X ^ %02X ==> %X\n", i, oldkey[i % key_length], _rotr(oldkey[i % key_length], (i % 8)), newkey[i]);
        }
        newkey[0x00] = oldkey[0];
        newkey[0x08] = oldkey[1];
        newkey[0x10] = oldkey[2];
        newkey[0x18] = oldkey[3];
        newkey[0x20] = oldkey[4];
        newkey[0x28] = oldkey[5];
        newkey[0x30] = oldkey[6];
        newkey[0x38] = oldkey[7];

        key_length = key_length * 8;

        free(oldkey);
        //        fprintf(stderr, "\n");
    }
    //fprintf(stderr, "key_length = %d\n", key_length);
    while ((readed_byte = fgetc(in)) != EOF) {
        read_byte = (unsigned char) readed_byte;
        //fprintf(stderr, "%02X[%08X] > %02X\n", read_byte, key_index, read_byte ^ (unsigned char) newkey[key_index]);
        if (silent == 1) {
            fputc(read_byte ^ (unsigned char) newkey[key_index], out);
        } else {
            fputc(read_byte ^ (unsigned char) key[key_index], out);
        }
        key_index++;
        key_index = key_index % key_length;
    }
    //fprintf(stderr, "key_length = %d\n", key_length);

    /*    if (silent == 1) {
     * free(key);
     * }
     */
    return EXIT_SUCCESS;
}

// vim: syntax=c ts=4 sw=4 sts=4 expandtab autoindent
