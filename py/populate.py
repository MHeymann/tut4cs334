#from __future__ import print_function
#from datetime import date, datetime, timedelta
import ConfigParser
import mysql.connector
import sys, getopt
import csv


multi = []
with open('questions_multi.csv', 'rb') as multifile:
    multi_reader = csv.reader(multifile)
    for row in multi_reader:
        multi.append({})
        if len(row) != 6:
            sys.exit("ERROR! length should be six: " + str(len(row)));
        multi[-1]['question'] = row[0];
        multi[-1]['opt1'] = row[1];
        multi[-1]['opt2'] = row[2];
        multi[-1]['opt3'] = row[3];
        multi[-1]['opt4'] = row[4];
        multi[-1]['ans'] = row[5];

written = []
with open('questions_word.csv', 'rb') as writtenfile:
    written_reader = csv.reader(writtenfile)
    for row in written_reader:
        written.append({})
        if len(row) != 2:
            sys.exit("ERROR! length should be two: " + str(len(row)));
        written[-1]['question'] = row[0];
        written[-1]['ans'] = row[1];




cfg = ConfigParser.ConfigParser()
try:
    cfg.read(sys.argv[1])
except:
    print "Error reading config file as provided in commandline"

try:
    cnx = mysql.connector.connect(user=cfg.get("client", "user"), password=cfg.get("client",
        "password"), database='tut4_db', host=cfg.get("client", "mysql_server"))
    cursor = cnx.cursor()


    add_user = ("INSERT INTO `user` "
            "(email, password) "
            "VALUES (%s, %s)")
    data_user = ('heymann.murray@gmail.com', 'Wilgers1')
    # Insert new user
    cursor.execute(add_user, data_user)
    cnx.commit()

    i = 0;
    for q in multi:
        i += 1
        add_q = ("INSERT INTO `multi_q` "
                "(question, opt1, opt2, opt3, opt4, ans) "
                "VALUES (%s, %s, %s, %s, %s, %s)")
        data_q = (q['question'], q['opt1'], q['opt2'], q['opt3'],
                q['opt4'], q['ans'])
        if ((i % 10) == 0):
            cnx.commit()
        cursor.execute(add_q, data_q)
    cnx.commit()

    i = 0;
    for q in written:
        i += 1
        add_q = ("INSERT INTO `written_q` "
                "(question, ans) "
                "VALUES (%s, %s)")
        data_q = (q['question'], q['ans'])
        if ((i % 10) == 0):
            cnx.commit()
        cursor.execute(add_q, data_q)


    # Make sure data is committed to the database
    cnx.commit()

    cursor.close()
    cnx.close()
except mysql.connector.Error as err:
    print "Error in mysql: {}".format(err)
except ConfigParser.NoSectionError as err:
    print "Error in config file: {}".format(err)
