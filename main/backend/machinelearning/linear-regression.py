import mysql.connector
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LinearRegression
from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
import json

def retrieve_data(connection):
    """Retrieve data from the MySQL database."""
    try:
        # Modify the SQL query to include MedicineName and relevant fields related to medicines
        sql = """
            SELECT mu.MedicineName, DATE(mu.UsageDate) AS UsageDate, SUM(mu.Quantity) AS TotalQuantity 
            FROM machinelearning mu
            GROUP BY mu.MedicineName, DATE(mu.UsageDate)
        """
        data = pd.read_sql(sql, connection)
        return data
    except Exception as e:
        print(f"Error retrieving data: {e}")
        return None

def preprocess_data(data, num_lags=3):
    """Preprocess the data by converting dates, creating lagged features, and handling missing values."""
    try:
        data['UsageDate'] = pd.to_datetime(data['UsageDate'])
        data.set_index('MedicineName', inplace=True)  # Set MedicineName as index
        for lag in range(1, num_lags + 1):
            data[f'TotalQuantity_lag_{lag}'] = data['TotalQuantity'].shift(lag)
        data.dropna(inplace=True)
        return data
    except Exception as e:
        print(f"Error preprocessing data: {e}")
        return None

def train_model(X, y):
    """Train a linear regression model."""
    try:
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=42)
        model = LinearRegression()
        model.fit(X_train, y_train)
        return model, X_test, y_test
    except Exception as e:
        print(f"Error training model: {e}")
        return None, None, None

def evaluate_model(model, X_test, y_test):
    """Evaluate the trained model."""
    try:
        y_pred = model.predict(X_test)
        # Ensure predictions are whole numbers and not negative
        y_pred = [max(0, round(pred)) for pred in y_pred]

        mse = mean_squared_error(y_test, y_pred)
        r2 = r2_score(y_test, y_pred)
        mae = mean_absolute_error(y_test, y_pred)
        rmse = mean_squared_error(y_test, y_pred, squared=False)
        print(f'Mean Squared Error: {mse}')
        print(f'R-squared (R2): {r2}')
        print(f'Mean Absolute Error: {mae}')
        print(f'Root Mean Squared Error: {rmse}')
        return mse, r2, mae, rmse
    except Exception as e:
        print(f"Error evaluating model: {e}")
        return None, None, None, None

def predict_next_day(model, latest_data_point):
    """Predict total quantity for the next day."""
    try:
        latest_features = latest_data_point.drop(['TotalQuantity', 'UsageDate'])
        latest_features = latest_features.values.reshape(1, -1)
        next_day_prediction = model.predict(latest_features)
        # Ensure prediction is a whole number and not negative
        next_day_prediction = max(0, round(next_day_prediction[0]))
        return next_day_prediction  # Return the prediction
    except Exception as e:
        print(f"Error predicting next day: {e}")

def predict_next_days(model, latest_data_point):
    """Predict total quantity for the next 7 days."""
    try:
        predictions = []
        latest_features = latest_data_point.drop(['TotalQuantity', 'UsageDate'])
        latest_features = latest_features.values.reshape(1, -1)
        for _ in range(7):
            next_day_prediction = model.predict(latest_features)
            # Ensure prediction is a whole number and not negative
            next_day_prediction = max(0, round(next_day_prediction[0]))
            predictions.append(next_day_prediction)
            # Shift the lagged features to the right and update the last lagged feature with the predicted value
            latest_features[0, 1:] = latest_features[0, :-1]
            latest_features[0, 0] = next_day_prediction
        return sum(predictions)  # Return the sum prediction for the next 7 days
    except Exception as e:
        print(f"Error predicting next days: {e}")

def main():
    """Main function to execute the workflow."""
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
                data = preprocess_data(data)

                # Dictionary to store predictions
                predictions = {'daily': {}, 'weekly': {}}

                # Iterate over each MedicineName
                for medicine_name, medicine_data in data.groupby(level=0):
                    print(f"Predictions for MedicineName {medicine_name}:")
                    # Split data into independent and dependent variables
                    X = medicine_data.drop(['TotalQuantity', 'UsageDate'], axis=1)
                    y = medicine_data['TotalQuantity']

                    # Train the model
                    model, X_test, y_test = train_model(X, y)
                    if model is not None:
                        # Evaluate the model
                        mse, r2, mae, rmse = evaluate_model(model, X_test, y_test)
                        print(f'Mean Squared Error: {mse}')
                        print(f'R-squared (R2): {r2}')
                        print(f'Mean Absolute Error: {mae}')
                        print(f'Root Mean Squared Error: {rmse}')

                        # Predict for the next day
                        latest_data_point = medicine_data.iloc[-1]
                        daily_prediction = predict_next_day(model, latest_data_point)
                        predictions['daily'][medicine_name] = daily_prediction

                        # Predict for the next 7 days (weekly prediction)
                        weekly_predictions = predict_next_days(model, latest_data_point)
                        predictions['weekly'][medicine_name] = weekly_predictions

                    else:
                        print("Failed to train model for MedicineName", medicine_name)

                # Close the database connection
                conn.close()

                # Convert predictions to JSON string
                predictions_json = json.dumps(predictions)
                print(predictions_json)  # Print JSON string

            else:
                print("Failed to retrieve data.")
        else:
            print("Failed to connect to the MySQL database.")
    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    main()
