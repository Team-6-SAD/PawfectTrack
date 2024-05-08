import mysql.connector
import pandas as pd
from sklearn.linear_model import LinearRegression
from datetime import datetime, timedelta
import json

def retrieve_patient_data(connection):
    """Retrieve patient data from the MySQL database."""
    try:
        # Modify the SQL query to group by DateAdded and calculate the count of patients for each day
        sql = "SELECT DateAdded, COUNT(*) AS PatientCount FROM patient_ml GROUP BY DateAdded"
        patient_data = pd.read_sql(sql, connection)
        return patient_data
    except Exception as e:
        print(f"Error retrieving patient data: {e}")
        return None

def preprocess_data(patient_data):
    """Preprocess patient data."""
    try:
        # Convert 'DateAdded' to datetime type
        patient_data['DateAdded'] = pd.to_datetime(patient_data['DateAdded'])
        # Set 'DateAdded' as the index
        patient_data.set_index('DateAdded', inplace=True)
        return patient_data
    except Exception as e:
        print(f"Error preprocessing data: {e}")
        return None

def train_model(X, y):
    """Train a linear regression model."""
    try:
        model = LinearRegression()
        model.fit(X, y)
        return model
    except Exception as e:
        print(f"Error training model: {e}")
        return None

def predict_next_day(model, latest_data_point):
    """Predict patient count for the next day."""
    try:
        latest_features = latest_data_point.index.astype('int64').values[-1].reshape(1, -1)
        next_day_prediction = model.predict(latest_features)
        return next_day_prediction[0]
    except Exception as e:
        print(f"Error predicting next day: {e}")
        return None

def predict_next_days(model, latest_data_point):
    """Predict patient count for the next 7 days."""
    try:
        predictions = []
        latest_features = latest_data_point.index.astype('int64').values[-1].reshape(1, -1)
        for _ in range(7):
            next_day_prediction = model.predict(latest_features)
            predictions.append(next_day_prediction[0])
            # Increment the date by one day
            latest_features[0, 0] += 1
        return sum(predictions)  # Return the sum prediction for the next 7 days
    except Exception as e:
        print(f"Error predicting next days: {e}")
        return None


def main():
    """Main function."""
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

            # Retrieve patient data
            patient_data = retrieve_patient_data(conn)
            if patient_data is not None:
                # Preprocess data
                patient_data = preprocess_data(patient_data)
                
                # Print patient data
                print("Patient Data:")
                print(patient_data)

                # Split data into independent and dependent variables
                X = patient_data.index.astype('int64').values.reshape(-1, 1)
                y = patient_data['PatientCount']

                # Train the model
                model = train_model(X, y)
                if model is not None:
                    # Get the latest data point
                    latest_data_point = patient_data.tail(1)

                    # Predict patient count for the next day
                    next_day_prediction = predict_next_day(model, latest_data_point)
                    print(f"Next Day Prediction - Predicted Patient Count: {next_day_prediction}")

                    # Predict patient count for the next 7 days (weekly prediction)
                    weekly_predictions = predict_next_days(model, latest_data_point)
                    print(f"Weekly Prediction - Predicted Patient Count: {weekly_predictions}")
                    
                    # Convert predictions to JSON
                    predictions_json = json.dumps({
                        "next_day_prediction": next_day_prediction,
                        "weekly_prediction": weekly_predictions
                    })
                    print("Predictions (JSON format):")
                    print(predictions_json)

                else:
                    print("Failed to train model.")
            else:
                print("Failed to retrieve patient data.")
        else:
            print("Failed to connect to the MySQL database.")
    except Exception as e:
        print(f"An error occurred: {e}")
    finally:
        # Close the database connection
        if conn and conn.is_connected():
            conn.close()

if __name__ == "__main__":
    main()
