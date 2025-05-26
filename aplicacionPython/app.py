#!/usr/bin/env python3
import json
import os
from typing import Any, Dict

import requests


def cargar_configuracion():
    """Cargar variables de entorno desde el archivo correspondiente"""
    # Determinar el modo de ejecución fff
    env_mode = os.environ.get("PYTHON_ENV", "development")

    # Seleccionar el archivo .env correspondiente
    env_file = ".env.production" if env_mode == "production" else ".env.development"

    print(f"Modo de ejecución: {env_mode}")

    # Cargar variables del archivo .env (si existe)
    try:
        from dotenv import load_dotenv

        if os.path.exists(env_file):
            load_dotenv(env_file)
        else:
            load_dotenv()  # Intentar con .env genérico
    except ImportError:
        # Si python-dotenv no está instalado, continuar sin él
        pass

    # Mostrar información de depuración si está habilitado
    if os.environ.get("DEBUG", "false").lower() == "true":
        print("Modo de depuración activado")
        print(f"Nivel de log: {os.environ.get('LOG_LEVEL', 'info')}")


def obtener_clima(ciudad: str) -> Dict[str, Any]:
    """Obtener información del clima para una ciudad específica"""
    # Obtener la clave API desde las variables de entorno
    api_key = os.environ.get("WEATHER_API_KEY")
    if not api_key:
        raise ValueError("No se encontró la variable WEATHER_API_KEY")

    url = f"https://api.weatherapi.com/v1/current.json?key={api_key}&q={ciudad}&lang=es"

    # Mostrar la URL en modo depuración
    if os.environ.get("DEBUG", "false").lower() == "true":
        print(f"DEBUG: Consultando URL: {url}")

    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        raise Exception(f"Error al consultar la API: {e}")


def mostrar_clima(tiempo: Dict[str, Any]):
    """Mostrar la información del clima en formato legible"""
    location = tiempo["location"]
    current = tiempo["current"]

    print(f"\nClima actual en {location['name']}:")
    print(f"País: {location['country']}")
    print(f"Región: {location['region']}")
    print(f"Temperatura: {current['temp_c']:.1f}°C")
    print(f"Sensación térmica: {current['feelslike_c']:.1f}°C")
    print(f"Humedad: {current['humidity']}%")
    print(f"Condición: {current['condition']['text']}")


def main():
    """Función principal de la aplicación"""
    # Cargar variables de entorno
    cargar_configuracion()

    print("Aplicación de Clima")
    print("-----------------")

    try:
        ciudad = input("Ingresa el nombre de una ciudad: ").strip()

        if not ciudad:
            print("Error: Debes ingresar el nombre de una ciudad")
            return

        tiempo = obtener_clima(ciudad)
        mostrar_clima(tiempo)

    except KeyboardInterrupt:
        print("\nAplicación interrumpida por el usuario")
    except Exception as e:
        print(f"Error: {e}")


if __name__ == "__main__":
    main()
