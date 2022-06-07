<?php

//Script by TadavomnisT

/*
*Note*
Which is the Best Cipher Mode and Padding Mode for AES Encryption?

Refrence: 
https://security.stackexchange.com/questions/52665/which-is-the-best-cipher-mode-and-padding-mode-for-aes-encryption

Author:
https://security.stackexchange.com/users/5400/polynomial


Answer from 2014:

Practices in cryptography have moved on a lot since I 
originally wrote this. I have added an update for 2021 further down.

"Best" is rather subjective - it depends on your requirements. 
That said, I'll give you a general overview of each mode.


ECB - Electronic Code Book. This mode is the simplest, and transforms 
each block separately. It just needs a key and some data, with no added 
extras. Unfortunately it sucks - for a start, identical plaintext blocks 
get encrypted into identical ciphertext blocks when encrypted with the 
same key. Wikipedia's article has a great graphic representation of 
this failure.

Good points: Very simple, encryption and decryption can be run in parallel.

Bad points: Horribly insecure.

CBC - Cipher Block Chianing. This mode is very common, and is considered 
to be reasonably secure. Each block of plaintext is xor'ed with the previous 
block of ciphertext before being transformed, ensuring that identical 
plaintext blocks don't result in identical ciphertext blocks when in 
sequence. For the first block of plaintext (which doesn't have a preceding 
block) we use an initialisation vector instead. This value should be unique 
per message per key, to ensure that identical messages don't result in 
identical ciphertexts. CBC is used in many of the SSL/TLS cipher suites.

Unfortunately, there are attacks against CBC when it is not implemented 
alongside a set of strong integrity and authenticity checks. One property 
it has is block-level malleability, which means that an attacker can alter 
the plaintext of the message in a meaningful way without knowing the key, 
if he can mess with the ciphertext. As such, implementations usually include 
a HMAC-based authenticity record. This is a tricky subject though, because 
even the order in which you perform the HMAC and encryption can lead to 
problems - look up "MAC then encrypt" for gory details on the subject.

Good points: Secure when used properly, parallel decryption.

Bad points: No parallel encryption, susceptible to malleability 
attacks when authenticity checks are bad / missing. But when done 
right, it's very good.

OFB - Output Feedback. In this mode you essentially create a stream 
cipher. The IV (a unique, random value) is encrypted to form the 
first block of keystream, then that output is xor'ed with the plaintext 
to form the ciphertext. To get the next block of keystream the previous 
block of keystream is encrypted again, with the same key. This is 
repeated until enough keystream is generated for the entire length of 
the message. This is fine in theory, but in practice there are questions 
about its safety. Block transforms are designed to be secure when 
performed once, but there is no guarantee that E(E(m,k),k) is secure for 
every independently secure block cipher - there may be strange 
interactions between internal primitives that haven't been studied properly. 
If implemented in a way that provides partial block feedback (i.e. only 
part of the previous block is bought forward, with some static or weakly 
random value for the other half) then other problems emerge, such as a 
short key stream cycle. In general you should avoid OFB.

Good points: Keystream can be computed in advance, fast hardware 
implementations available

Bad points: Security model is questionable, some configurations lead 
to short keystream cycles

CFB - Cipher Feedback. Another stream cipher mode, quite similar to 
CBC performed backwards. Its major advantage is that you only need 
the encryption transform, not the decryption transform, which saves 
space when writing code for small devices. It's a bit of an oddball 
and I don't see it mentioned frequently.

Good points: Small footprint, parallel decryption.

Bad points: Not commonly implemented or used.

CTR - Counter Mode. This essentially involves encrypting a sequence 
of incrementing numbers prefixed with a nonce (number used once) to 
produce a keystream, and again is a stream cipher mode. This mode 
does away with the problems of repeatedly running transforms over 
each other, like we saw in OFB mode. It's generally considered a good mode.

Good points: Secure when done right, parallel encryption and decryption.

Bad points: Not many. Some question the security of the "related plaintext" 
model but it's generally considered to be safe. (Update, 2021: I'm not sure 
why I didn't mention this back in 2014, but stream ciphers are inherently 
malleable, meaning that an attacker can flip arbitrary bits in your plaintext 
if you fail to verify the integrity and authenticity of the ciphertext properly)

Padding modes can be tricky, but in general I would always suggest PKCS#7 padding,
which involves adding bytes that each represent the length of the padding, e.g. 
04 04 04 04 for four padding bytes, or 03 03 03 for three. The benefit over some 
other padding mechanisms is that it's easy to tell if the padding is corrupted - 
the longer the padding, the higher the chance of random data corruption, but it 
also increases the number of copies of the padding length you have. It's also 
trivial to validate and remove, with no real chance of broken padding somehow 
validating as correct.

In general, stick with CBC or CTR, with PKCS#7 where necessary (you don't need 
padding on stream cipher modes) and use an authenticity check (HMAC-SHA256 for 
example) on the ciphertext. Both CBC and CTR come recommended by Niels Ferguson 
and Bruce Schneier, both of whom are respected cryptographers.

That being said, there are new modes! EAX and GCM have recently been given a 
lot of attention. GCM was put into the TLS 1.2 suite and fixes a lot of 
problems that existed in CBC and stream ciphers. The primary benefit is 
that both are authenticated modes, in that they build the authenticity 
checks into the cipher mode itself, rather than having to apply one separately. 
This fixes some problems with padding oracle attacks and various other trickery. 
These modes aren't quite as simple to explain (let alone implement) but they 
are considered to be very strong.

Update for 2021:

As of November 2021, CBC is generally no longer recommended for use 
in new systems. You should use an AEAD such as ChaCha20-Poly1305 or AES-GCM.

CBC is highly fraught in practice because you must be very careful 
about the padding scheme you choose and the integrity and authenticity 
checks you apply. If the authenticity record is applied to the 
plaintext instead of the ciphertext (i.e. MAC-then-encrypt) then 
an attacker may be able to utilise a padding oracle side-channel 
attack to decrypt data by repeatedly sending modified packets to 
a receiver that attempts to decrypt them. If an attacker can modify
the IV in transit, because it wasn't protected by an authenticity 
record, they can modify the first decrypted block of plaintext by 
manipulating the IV. The padding scheme itself must also be chosen 
to be resistant to ambiguous decoding, e.g. an all-zero padding scheme 
would result in any trailing zeroes in the actual message to be stripped.

CTR is less fraught. There's no padding, since CTR ciphertexts are the 
same length as their plaintexts. An attacker who modifies the IV can 
only garble the whole message - modifying the IV doesn't allow them to 
make meaningful modifications. However, stream ciphers still have 
bit-level malleability, meaning that a ciphertext that is not authenticated 
(e.g. with a MAC) can be manipulated to flip arbitrary bits in the 
plaintext, at any position. Whereas in CBC the use of MAC-then-encrypt is
liable to completely break the system, in CTR mode (and in other stream 
ciphers) it not guaranteed to be as catastrophic.

AES-CBC is still widely used in TLS 1.2, but it has taken many years of 
careful engineering to make that implementation safe enough for general 
use. Implementing CBC mode in your own system is ill-advised. All CBC mode 
cipher suites have been removed from TLS 1.3. You should consider it to be 
deprecated, and use it only where you must interoperate with a system that 
cannot be upgraded to use a more modern scheme.

AES-CTR is also still around, but again is generally not recommended for new 
designs. It's maybe less problematic than CBC, but if we're talking about 
picking a cipher suite for TLS then there's barely any difference.

AEADs are now preferred. AEAD stands for Authenticated Encryption with 
Associated Data. What this means, in practice, is that you can encrypt 
some message (thereby providing confidentiality), authenticate it (thereby 
providing integrity and authenticity), and also authenticate some associated 
data that does not require confidentiality, all within the cipher mode 
itself. This removes the burden of implementing authenticity validation 
separately. The associated data is often used to attach an IV or other 
protocol-specific data to the encrypted message in a way that prevents 
tampering, which again alleviates the need to implement this separately.

AES-GCM is an AEAD based on AES-CTR and Galois Message Authentication 
Code (GMAC) for message authentication. It is supported in TLS 1.2 and 
1.3 and offers a meaningful security upgrade from CBC and CTR modes. 
Modern x86 processors, and higher-power ARM processors, include specialised 
instructions that accelerate both AES encryption/decryption operations and 
Galois field calculations, making AES-GCM very fast on these platforms. 
A downside to GCM is that it is tricky to implement safely, and it is very 
unforgiving if it fails. Neither of these things are a concern if you're 
just consuming AES-GCM in a protocol like TLS, but they are issues that 
matter to cryptographers.

CCM is another AEAD, this time based on a combination of CBC and CBC-MAC. 
While CBC itself is considered weak, for the reasons I described above, 
the construction of CCM does not fall foul of those specific problems. 
The CBC-MAC part provides authenticity, and is itself constructed from 
CBC operations. CCM is viewed somewhat less favourably than other AEADs 
in terms of the cryptography, but it saves you implementing Galois field 
operations on platforms that don't have acceleration instructions for it. 
AES-CCM is available in both TLS 1.2 and TLS 1.3. There is also a variant 
with a truncated authentication tag, referred to as CCM_8, which uses an 
8-byte (64-bit) authentication tag rather than the usual 16-byte (128-bit) 
authentication tag - you can read about this here.

I mentioned EAX mode previously. This hasn't really caught on. It's far 
slower than GCM. There's also a variant called "EAX(prime)", which is 
completely broken.

Another lesser-used AEAD is Offset Codebook Mode (OCB). This was, in 
theory, a good AEAD cipher mode. It uses Galois fields, but it's easier 
to implement than GCM. There are three variants. OCB1 is not an AEAD. 
OCB2 adds AEAD functionality. OCB3 is a newer version. OCB2 is broken - 
there's a full plaintext recovery attack on it. OCB3 is still considered 
secure. None of this really matters in practice, however, because author 
patented it and applied a restrictive exemption clause that allowed free 
implementation only in GPL licensed code. This was later relaxed to any 
open source license approved by the OSI, but this remained a significant 
practical encumbrance. The patents were abandoned by February 2021, but 
by this point everybody had standardised on other modes. OCB is largely 
irrelevant now, despite potentially being better than GCM.

ChaCha20-Poly1305 is currently (as of November 2021) regarded as one of 
the better AEADs. It isn't strictly a "cipher mode", because it's a 
specific combination of a cipher and a MAC - it is constructed from the 
ChaCha20 stream cipher (a variant of Salsa20) and the Poly1305 message 
authentication code. It is generally faster and more power-efficient 
than AES-GCM and AES-CCM when specialised hardware acceleration is not 
available. It has strong security properties and attractive practical 
properties for implementers. It is available in TLS 1.2 and TLS 1.3, 
and is currently the default cipher used in libsodium's secret box API. 
You should prefer it in new systems.

In summary:

    ECB - Don't use it. There are a very small number of situations where ECB is the correct choice, but unless you're a cryptographer you'll almost never run into them.
    CBC - Theoretically secure, but difficult to get right in practice. Has been the cause of many vulnerabilities in TLS over the years, due to the use of MAC-then-CBC. Consider this to be deprecated. Acceptable in legacy systems that have careful implementations, but should not be used in new designs.
    OFB - Don't use it. It's an unusual mode that lacks desirable properties and has issues with cycle length.
    CFB - Don't use it. Again, it's an unusual mode.
    CTR - Theoretically secure, and fewer footguns than CBC. Still needs careful implementation to provide strong authenticity. Consider this to be deprecated. Acceptable in legacy systems that have careful implementations, but should not be used in new designs.
    GCM - A strong, widely supported AEAD. Tricky to implement safely if timing and power analysis side-channel attacks are a concern, but if you're using it in a standard protocol implementation (e.g. openssl) it's fine. Appropriate for new designs.
    CCM - Another strong, widely-supported AEAD. Can be a little slow, but easier to implement than AES-GCM and supports truncated tags. Appropriate for new designs, but generally not the first choice.
    EAX - No reason to use this mode. It's no more secure than GCM, which is faster.
    OCB - Theoretically a good AEAD, but patent encumbrance prevented it from catching on. Maybe it'll take off in future? For now you can ignore it.
    ChaCha20-Poly1305 - A specific cipher and MAC pair rather than a mode, but it's one of the best options available as of November 2021 if you're trying to pick a cipher suite. Widely supported. I'd recommend making it your preferred option.



////////////////////////////////////////////////////////////////////////////////////


So... what should we do?!


*/


?>