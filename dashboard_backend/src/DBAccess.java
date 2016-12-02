/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;
import java.util.TreeMap;
import java.util.logging.Logger;
import javax.mail.MessagingException;

import utility.Utility;

public class DBAccess {

  private Connection connect = null;
  private Statement statement = null;
  private PreparedStatement preparedStatement = null;
  private ResultSet resultSet = null;
  private static final Logger logger = Logger.getLogger(DBAccess.class.getName());
  private String[] paramsEmail = null;

  public DBAccess() {

  }

  public DBAccess(String[] params) {
    paramsEmail = params;
  }

  public void setConnectionMySQL(String[] accessValues) {
    //String url = "jdbc:mysql://192.168.0.20:3306/";

    String urlFixed = (accessValues[0].trim()) + "/" + (accessValues[1].trim());
    String username = accessValues[2].trim();
    String password = accessValues[3].trim();

    try {

      Class.forName("com.mysql.jdbc.Driver");
      // Setup the connection with the DB
      connect = DriverManager.getConnection(urlFixed, username, password);
    } catch (Exception exp) {
      //System.out.println(exp.getMessage());
      DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
      Date data_attuale = new Date();
      String data_fixed = df.format(data_attuale);
      String msgBody = "You're receiving this email from Km4City Dashboard because an error has occurred when the metrics calculation software has tried ";
      msgBody += "to establish a connection to the database. This error is generated by " + exp.getClass().getSimpleName() + " exception thrown at " + data_fixed;
      msgBody += "\n\nError Details:";
      msgBody += "\nDescription: Could not connect to database having the following url: " + urlFixed;
      msgBody += "\nException: " + exp.getMessage().replace("\n", " ");
      if(exp.getCause()!=null)
        msgBody += "\nCause: " + exp.getCause().getMessage().replace("\n", " ");
      msgBody += "\nError Trace: See LogFile.log for deatils";
      msgBody += "\nJava Class: " + this.getClass().getName();
      msgBody += "\nDate: " + data_fixed;
      msgBody += "\n\nThis message is generated by the Km4City Dashboard. For support: info@disit.org";
      msgBody += "\nPlease do not reply to this message.";
      Utility.WriteExcepLog(logger, exp);
      Utility.SendEmail(paramsEmail, msgBody, "[EXCEPTION] Km4city dashboard");
    }
  }

  public ResultSet readDataBase(String query) {
    try {
      statement = connect.createStatement();
      System.out.println("Query: " + query);
      resultSet = statement.executeQuery(query);
      System.out.println("Query di lettura eseguita correttamente");
      return resultSet;
    } catch (Exception exp) {
      System.out.println("Errore in metodo readDataBase");
      //Utility.WriteExcepLog(logger, exp);
      return null;
    }
  }

  public void writeDataBaseData(String query) {
    try {
      statement = connect.createStatement();
      System.out.println("Query: " + query);
      statement.executeUpdate(query);
      System.out.println("Query di scrittura eseguita correttamente");
    } catch (Exception exp) {
      System.out.println("Errore in metodo writeDataBase");
      //Utility.WriteExcepLog(logger, exp);
    }
  }

  /*public ResultSet readCodCorsa() {
   try {
   statement = connect.createStatement();
   // Result set get the result of the SQL query
   resultSet = statement.executeQuery("SELECT COUNT(DISTINCT cod_corsa) AS Sum FROM SiiMobility.Code_corsa where cod_linea ='6' OR cod_linea ='17' OR cod_linea ='4'");
   //writeResultSetDescriptions(resultSet);
   return resultSet;
   } catch (Exception e) {
   System.out.println(e.getMessage());
   return null;
   }
   }

   public ResultSet readNodesSce() {
   try {
   statement = connect.createStatement();
   // Result set get the result of the SQL query
   resultSet = statement.executeQuery("SELECT a1.ID, a1.DATE, a1.IP_ADDRESS, a1.SCHEDULER_INSTANCE_ID as IdNode "
   + "FROM quartz.QRTZ_NODES a1 INNER JOIN (SELECT max(id) maxId, IP_ADDRESS FROM quartz.QRTZ_NODES GROUP BY IP_ADDRESS) a2 ON a1.IP_ADDRESS = a2.IP_ADDRESS AND a1.id = a2.maxId HAVING date(a1.DATE) >= NOW() - INTERVAL 1 day ORDER BY a1.DATE DESC");
   //writeResultSetDescriptions(resultSet);
   return resultSet;
   } catch (Exception e) {
   System.out.println(e.getMessage());
   return null;
   }
   }*/
  public void close() {
    try {
      if (resultSet != null) {
        resultSet.close();

      }
      if (statement != null) {
        statement.close();

      }
      if (connect != null) {
        connect.close();
      }
    } catch (Exception exp) {
      System.out.println(exp.getMessage());
      Utility.WriteExcepLog(logger, exp);
    }
  }

  public ResultSet getResultSet() {
    return resultSet;
  }
}
