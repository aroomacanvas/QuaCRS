﻿   ___               ____ ____  ____  
  / _ \ _   _  __ _ / ___|  _ \/ ___| 
 | | | | | | |/ _` | |   | |_) \___ \ 
 | |_| | |_| | (_| | |___|  _ < ___) |
  \__\_\\__,_|\__,_|\____|_| \_\____/ 
                                    
                   ___
                 .'   '.__
                (    o {__)
                 \  ._.'
          /\      )  (
         |  ``'"'`    \
         \  {`'-       }
          '. '-._     /
   ``^^`^`^`^^`^^^``^`^``^``^^^


The Ohio State University Wexner Medical Center
Version 1.1
6/27/2014

TABLE OF CONTENTS 
----------------
* Introduction
* Version History
* Requirements
* Installation
* Database Setup
* QC Configuration 
* Usage
* Troubleshooting
* Maintainers

INTRODUCTION
----------------
	The first part of this workflow is QC Pack, a wrapper for three popular RNA-Seq quality control tools; RNA-SeQC, RSeQC, and FastQC. QC Pack runs on one sample at a time. It requires an aligned BAM file, one or two raw FASTQ files, and a configuration file containing additional metadata. Much of this configuration information is optional. Sequencing date, sequencing lane, sample ID, and a study descriptor are all required. 

	The last part of the workflow summarizes the QC plots and metrics in a database. Users can navigate the data in an interactive, HTML format. Results can then be filtered, searched, and downloaded. 

VERSION HISTORY
----------------
QuaCRS v1.1 (Released 6/27/14)
       * Added Readme page
       * Removed highlighting on searches
       * Fixed PHP error when attempting to create an aggregate report of 0 samples
       * Added 'Sequencing date' column

QuaCRS v1.0 (Released 5/29/14)
       * Initial release

REQUIREMENTS
----------------
QC tools
* RNA-SeQC (http://www.broadinstitute.org/cancer/cga/rna-seqc)
* RSeQC (http://rseqc.sourceforge.net/)
* FastQC (http://www.bioinformatics.babraham.ac.uk/projects/fastqc/)

Other dependencies
* samtools v0.1.10 or newer (http://sourceforge.net/projects/samtools/files/)
* picardtools v1.94 or newer (http://sourceforge.net/projects/picard/files/)
* Reference annotation (GTF & BED), and accompanying FASTA file
	Tested with Gencode 19 (http://www.gencodegenes.org/releases/19.html)
	Users may use the Galaxy Convert Format tool to convert the Gencode GTF to a BED file (https://usegalaxy.org/)
* MySQL database (http://dev.mysql.com/doc/refman/5.6/en/installing.html)
* Local Server with PHP installed (http://us2.php.net/manual/en/install.php)
	PHP version 5.1.6 or newer
* python (https://wiki.python.org/moin/BeginnersGuide/Download)
	MySQLdb module
* python-dev
* ncurses
* ImageMagic convert (http://www.imagemagick.org/script/convert.php)

INSTALLATION
----------------
1) Unzip the tar archive 
2) Edit the QC tools configurations file (tools.cfg) to reflect the installation locations of samtools, picard tools, RNA-SeQC, RSeQC, FastQC, and the reference FASTA, GTF, and BED files. These can be downloaded and installed anywhere, as long as the full path is supplied here. This file must remain in the same directory as qcpack.sh.
3) Copy the 'qc' folder to the server location (usually /var/www/)

DATABASE SETUP
----------------
	First, create a database with the default privileges. The name of the database will be configured in settings below. For more information on how to create databases, visit http://dev.mysql.com/doc/refman/5.0/en/creating-database.html

	READ PROGRAM
	----------------
	Open the config.py file in the 'constant' folder. Set the database credentials using the DB_HOST, DB_USER, DB_PASS, DB_PORT, and DB_NAME variables.
	DB_PORT is optional
	DB_HOST should be "localhost" if using a database setup locally.

	In that same file, set the `WEB_APP_PATH` variable to the location of the 'qc' folder. For example, if the 'qc' folder is in the root of the localhost folder, WEB_APP_PATH = /var/www/qc/ (make sure to include the trailing slash)

	WEB ('qc' folder)
	----------------
	Open the config.php file in qc/application/config folder. In that file, change $config['base_url'] and $config['root'] if they are different from how the server was set up
	
	$config['base_url']: Base URL should be absolute, including the protocol. This is the url of the project folder, the same address used access the database with an Internet browser.
	$config['root']: Root should be absolute (make sure to include the trailing slash). This is the path to the web folder, which should be identical to WEB_APP_PATH.

	Next, open the database.php file in the same folder (application/config). In that file, fill in the database authentication configurations. The variables $db['default'] index 'hostname', 'username', 'password', and 'database' need to be filled in (these fields should be identical to what was setup in the Read Program section).
	For more information on setting up the database configuration, visit http://ellislab.com/codeigniter/user-guide/database/configuration.html

QC CONFIGURATION
----------------
	The QC wrapper is run 1 sample at a time with 1 configuration file as an argument. A sample configuration file is included with QC Pack (input.cfg).

The following configuration fields are required:

	FASTQ_FILE= Full file path to where the FASTQ file is located. If this sample is a paired end sequencing sample, supply a comma separated list of paths with no spaces.

	BAM_FILE= Full file path where the aligned BAM file is located. 

	SID= Sample Identification, or sample name.

	STUDY= Name of a project with which the sample is associated.

	DATE= Sequencing date. Can be another important date. Used to uniquely identify multiple runs of the same sample. Left blank only for combined samples

	LN= Sequencing lane. Can be another important identifier. Used to uniquely identify different runs of the same sample. Left blank only for combined samples

	RUN_DESCRIPTION= Used to identify samples that are combined from more than one sequencing run. If reads come from more than one run, the raw files and aligned files will contain reads from more than one lane and date. Such samples require the following additional considerations:

	1) The FASTQ files must be combined before entering this QC workflow. 
		For single read experiments, simply concatenate the two runs into a composite FASTQ file
		For paired end data, concatenate the left mates separately from the right mates to yield two composite FASTQ files
 	2) When a sample is combined from multiple flowcell dates, this field must be "COMBINED"
		If not a combined sample, this field can be any string, or empty. 

The following configuration fields are optional:

	INDEX= Bar code sequence used for demultiplexing 

	RQS= RNA quality score

	SEQUENCING_TYPE= PolyA, Exome, Transcriptome, Genome, etc. 

	FCN= Flowcell number (the number of times the sample has been sequenced)

USAGE
----------------
	Once the sample configuration files are complete, run the wrapper as follows:

	$ bash qcpack.sh input.cfg

	qcpack.sh and input.cfg do not need to be in the current working directory, or even the same directory. The program will  output to the current working directory. 

	Under normal circumstances, qcpack can check for existing output and resume incomplete steps. If QC fails, it may be necessary to run qcpack with the option to force removal of temporary files and existing output. This is done by passing "force" as an additional argument:

	$ bash qcpack.sh input.cfg force

	Multiple samples may be processed in parallel, assuming the hardware will support it. 

	A QC run will create FastQC, RSeQC, and RNASeQC directories in the working directory if they do not already exist. Each of these will contain a directory for the individual sample with the associated QC output. Each sample will also have a unique QC table to be read by the database. This table is a compilation of many QC metrics to summarize in the graphical user interface. Once all samples are finished processing, they are ready to upload to the database.

Assumptions for example purposes:
	The result of the QC Pack is stored at ~/Documents/qc_pack/result/
	The Read program is stored at ~/Documents/read/ and configured correctly with the database and local server.
	The 'qc' folder (WEB) is copied in the root directory of the local server and configured correctly. $config['base_url']='http://localhost/qc/';

Step-by-step execution (for a single sample):
	1) Navigate to where the Read program is located: cd ~/Documents/read/
	2) Execute the read program(do not copy the dollar sign): $ python read.py -i ~/Documents/qc_pack/result/<SAMPLE_QC_TABLE.CSV> -d '\t' create

	-i gives the program the path to the QC table (including trailing slash).
	-d sets the delimiter for those files. The default delimiter is ',' (comma).

Step-by-step execution (for multiple samples):
	1) Navigate to where the Read program is located: $ cd ~/Documents/read/
	2) Execute the read program: $ python read.py -b ~/Documents/qc_pack/result/ -d '\t' create

	-b gives the program the path to the QC tables (including trailing slash).
	-d sets the delimiter for those files. The default delimiter is ',' (comma).

	For more information on executing the Read program: $ python read.py --help

Output:
	Upon completion, the program will output how many samples were successfully processed and how many samples failed.

	If the program returns an error, make sure that the database is setup correctly and make sure that the qc tables are located in the directory specified. Before executing the program again, clear the database: $ python read.py clear

TROUBLESHOOTING
----------------
1) What are the MySQL warnings for?
	These warnings indicate that the QC tables contain more significant digits than what is defined in the database.
2) Why does RSeQC return so many errors regarding my reference file?
	Using the Galaxy Convert Format tool to convert the Gencode GTF file to BED format will loosely translate many GTF lines into incomplete BED entries. This doesn't prevent RSeQC from running successfully, but may result in large amounts of warning messages. Removing the truncated lines from the BED file should fix this problem.

MAINTAINERS
----------------
As of 5/28/2014:
	Nima Esmaili Mokaram - esmailimokaram.1@buckeyemail.osu.edu	
	Karl Kroll - Karl.Kroll@osumc.edu
	Alex Pelletier - Alex.Pelletier@osumc.edu

