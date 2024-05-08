import mysql.connector
import pandas as pd
from sklearn.linear_model import LinearRegression
import sys

def retrieve_data(connection):
    try:
        sql = """
            SELECT mu.MedicineBrandID, m.MedicineID, m.MedicineName, YEARWEEK(mu.UsageDate) AS UsageWeek, SUM(mu.Quantity) AS TotalQuantity 
            FROM machinelearning mu
            INNER JOIN medicinebrand mb ON mu.MedicineBrandID = mb.MedicineBrandID
            INNER JOIN medicine m ON mb.MedicineID = m.MedicineID
            GROUP BY mu.MedicineBrandID, m.MedicineID, YEARWEEK(mu.UsageDate)
        """
        data = pd.read_sql(sql, connection)
        print("Retrieved data:")
        print(data.head())  # Print first few rows of retrieved data
        return data
    except Exception as e:
        print(f"Error retrieving data: {e}")
        return None

def train_model(X, y):
    """Train a linear regression model."""
    try:
        # Check if 'TotalQuantity' column exists in X
        print("Columns of X before dropping:")
        print(X.columns)
        
        if 'TotalQuantity' in X.columns:
            # Drop irrelevant columns from the features
            X = X.drop(['TotalQuantity', 'UsageWeek', 'MedicineBrandID', 'MedicineID'], axis=1)
            
            print("Columns of X after dropping:")
            print(X.columns)
            
            model = LinearRegression()
            model.fit(X, y)
            return model
        else:
            print("Column 'TotalQuantity' not found in the DataFrame.")
            return None
    except Exception as e:
        print(f"Error training model: {e}")
        return None

def main():
    try:
        # Connect to the MySQL database
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="pawfect"
        )

        if conn.is_connected():
            print("Connected to the MySQL database")

            # Retrieve and preprocess the data
            data = retrieve_data(conn)
            if data is not None:
                if not data.empty:  # Check if data is not empty after preprocessing
                    # Print data types of all columns
                    print("Data types of columns:")
                    print(data.dtypes)
                    
                    # Define features and target
                    X = data.drop(['TotalQuantity'], axis=1)
                    y = data['TotalQuantity']

                    # Train the model
                    model = train_model(X, y)

                    if model is not None:
                        print("Model trained successfully.")
                    else:
                        print("Failed to train model.")

                else:
                    print("No valid data available after preprocessing.")
            else:
                print("Failed to retrieve data.")
        else:
            print("Failed to connect to the MySQL database.")
    except Exception as e:
        print(f"An error occurred: {e}")
        print("Error occurred at line:", sys.exc_info()[-1].tb_lineno)  # Print line number where error occurred

if __name__ == "__main__":
    main()
