
úÄÄÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÄÄú

  Ü      Ü                                 Ü          Ü       Ü
  ßÛÛÛÛÛÛß                          ÛÜÜÜÜÛ ÛÛÛÛÛÛÛÛÜ  ÛÛÛÜÜÜÜÛÛ
   Û²²²²Û                           ßÛ²²Ûß Û²²²²²²²ÛÛ ßÛ²²²²²Ûß
    Û²°Û                             Û²²Û  ßÛ²²°°°²²Û  Û²²²²²ÛÜ
    Û²°Û                             Û²²Û   Û²²²²²²ÛÛ  ßÛ²²ÛÛÛÛ
    Û²°Û       Ü      Ü Ü   Ü        Û²²Û   Û²²ÛÛÛßß    Û²²ÛÜÜ
    Û²°Û       ÛÛÜÜÜÜÛÛ ßÛÛÛß ÜÜ ÛÜÜÛÛ²²Û   Û²Û         Û²²²²ÛÜ
    Û²°Û       ßÛ²²²²Ûß  Û²ÛÜÛÛÛ ßÛ²²²²²Û   Û²Û         Û²²²²Ûß
    Û²°Û    ÜÜ  Û²°°²Û   Û²²²Ûßß  Û²°°°²Û   Û²²Û        Û²²Ûßß
   Û²²°°ÛÛÛÛÛ   Û²°°²Û   Û²Ûß     Û²°°°²Û   Û²²²Û      ÜÛ²²ÛÛÛÛ
   Û²²²°°°°Û   ÜÛ²²²²ÛÜ ÜÛ²ÛÜ     Û²²²²²Û  ÜÛÛÛÛÛÜ     Û²²²²²Ûß
  ÛÛÛÛÛÛÛÛÛÛÛ  ÛÛßßßßÛÛ ÛßßßÛ    ÛÛßßßßßÛÛ Ûß   ßÛ    ÜÛÛßßßßÛÛ
 ßß         ßß ß      ß ß   ß    ß       ß ß     ß    ß       ß
                                                              [yoda]

úÄÄÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÄÄú


version:         RoyalTS
coder:           yoda
project start:   25th march 2001
coding language: C (16Edit.dll in C++)
E-mail:          LordPE@gmx.net
website:         y0da.cjb.net


Why LordPE ???:
---------------
"PEditor" is dead since some time :((( It wasn't updated anymore.
It's coded in a very bad way. M.o.D. and I were very young coders
as we started to develope it ;) That's one reason for me not to add
code to PEditor anymore.
As it seems that some people are using it, I tried to recode all the
stuff. It's name is "LordPE". I decided to code this project in C
because a big part of it is GUI shit.
LordPE is not finished up to now...
I hope some people will like it ;)


Features:
---------
- Task viewer
  - dump modules full
  - dump modules partial
  - dump process regions
  - modify process priority
  - anti dump protection stuff
  - kill processes

- PE Editor
  - edit basic Header information
    - edit SubSystem flag
    - correct checksum
    - edit characteristics
  - enlarge header
  - Section Table viewer
    - edit Section Headers
      - edit Section Header characteristics
	- hex edit Section
    - add Section Headers
    - delete Section Headers
    - save Sections to disk
    - load Sections from disk
    - truncate at the start/at the end of a Section
    - Split/Unsplit
  - Directory Table viewer
    - Export Table viewer
      - edit Export Table
      - edit Exported items    
    - Import Table viewer
      - edit ImageImportDescriptors
      - delete ImageImportDescriptors
      - delete OriginalFirstThunk's
      - add imports
      - edit thunks of ImageImportDescriptors
    - Resource Directory viewer
      - dump resources
	  - hex edit resources
    - Advanced Relocation viewer
    - Copyright string viewer
    - Tls Table viewer
    - Debug Directory viewer
    - Bound Import viewer
  - FLC (VA<->RVA<->Offset - calculator)
    - hex edit target location
  - TDSC (TimeDateStamp <-> time/date - converter)
  - compare PE files

- Break & Enter
  - break at the EntryPoint of PE exe or dll files

- PE Rebuilder
  - dumpfix
  - realigning
  - wipe relocation
  - ImportTable rebuilder
  - validate PE (make a PE work on win2k)
  - Bind Imports
  - Change ImageBase
  
- Dumper Server (plugin interface)


General notes:
--------------
- Break&Enter:
   - Sometimes you have to scroll up a bit to see the original
     byte in your debugger.
   - If you want to dump a dll then assemble e.g. a "jmp eip"
     in it and click in LordPE's main window on the process
     "TrapDll.exe". In its module list you'll find the target
     dll which you can dump.

- Task viewer:
   - "Correct ImageSize" kills the dump protection, based on
     modifing the ImageSize value of the internal windows variables,
     by ANAKiN. This technic is e.g. used in PEShield
   - It's possible to dump only sections from process modules in
     memory by loading the file into the PE editor (via temporary file!)
     and then you can save any section inside the section table viewer
     as usual to disk          

- PE editor:
   - If LordPE couldn't get write access to the target file then the
     file is opened in read only mode. In this case all "save"-
     buttons and so on are disabled.
   - you can resize splitted sections before unsplitting
   - To be able to use the TDSC, you need to be installed Internet
     Explorer 4 or higher !   
   - With the "+" beside the SizeOfHeaders edit box one can make the
     PE header grow in 0x200 bytes steps. This could be useful e.g. if
     a packer/compressor reports about not enough room in the section
     table or in the PE header.


LordPE.exe command line:
------------------------
/NOTRADEMARK
Avoids LordPE from pasting a trademark into PE files. This is usually 
done after the following things:
 - full module dump
 - rebuilding
 - add imports
 - doing a full dump with the Dumper Server

/BREAKENTER"%path%"
Break&Enter at the specified file. The main dialog won't be shown.
%path% - path to a dll or exe file whose EntryPoint should be trapped

/PEEDIT"%path%"
Opens up the PE editor. The main dialog won't be shown.
%path% - path to file to edit with PE editor

/LDS%modifiers%
Lunches the DumperServer. The main dialog won't be shown.
%modifiers% - could be "+L" / "-L" (enable/disable request logging) and/or
              "+T" / "-T" (enable/disable topmost)


THX:
----
MackT        - for BETA testing + good ideas + a big bag full of bugfixes
bart         - for giving many nice improvement ideas
Snow Panther - bug reports


Your improvements, suggestions and bugfixes are welcomed.
Have fun !
yoda