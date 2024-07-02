import pandas as pd
import mysql.connector
from statsmodels.tsa.arima.model import ARIMA
import matplotlib.pyplot as plt
import traceback
from statsmodels.graphics.tsaplots import plot_acf, plot_pacf
from sklearn.metrics import mean_absolute_error, mean_squared_error
import numpy as np

# Function to train ARIMA model
def train_arima_model(data):
    try:
        p = 3
        d = 3
        q = 1

        model = ARIMA(data['TotalQuantity'], order=(p, d, q))
        fitted_model = model.fit()
        return fitted_model
    except Exception as e:
        print(f"Error training ARIMA model: {e}")
        return None

# Function to predict for the next day using ARIMA model
def predict_next_day_arima(model, data):
    try:
        print("Making predictions for the next day...")
        print("Last observed date:", data['UsageDate'].iloc[-1])
        print("Data used for prediction:")
        print(data.tail())
        next_day_prediction = model.forecast(steps=1)
        print("Next day prediction:", next_day_prediction)
        
        if not next_day_prediction.empty:
            next_day_prediction_list = next_day_prediction.tolist()
            print("Prediction for the next day:", next_day_prediction_list[0])

            # Calculate evaluation metrics
            actual = data['TotalQuantity'].iloc[-1]  # Last observed value
            predicted = next_day_prediction_list[0]  # Predicted value

            mae = mean_absolute_error([actual], [predicted])
            mse = mean_squared_error([actual], [predicted])
            rmse = np.sqrt(mse)

            print("Mean Absolute Error (MAE):", mae)
            print("Mean Squared Error (MSE):", mse)
            print("Root Mean Squared Error (RMSE):", rmse)
        else:
            print("No prediction available.")
    except Exception as e:
        print("Error predicting next day with ARIMA model.")
        print("Error message:", str(e))
        print("Type of error:", type(e).__name__)
        print("Traceback:", traceback.format_exc())

# Function to retrieve data from MySQL database
def retrieve_data(connection):
    try:
        sql = "SELECT DATE(UsageDate) AS UsageDate, SUM(Quantity) AS TotalQuantity FROM machinelearning GROUP BY DATE(UsageDate)"
        data = pd.read_sql(sql, connection)
        return data
    except Exception as e:
        print(f"Error retrieving data: {e}")
        return None

# Main function
def main():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="pawfect",
            password="EJHts0D5qExNa9P4IOAt",
            database="pawfect"
        )

        if conn.is_connected():
            print("Connected to the MySQL database")
            
            data = retrieve_data(conn)
            if data is not None:
                model = train_arima_model(data)
                if model is not None:
                    predict_next_day_arima(model, data)
                else:
                    print("Failed to train ARIMA model.")
            else:
                print("Failed to retrieve data.")
                
            conn.close()
        else:
            print("Failed to connect to the MySQL database.")
    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    main()
