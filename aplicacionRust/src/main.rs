use dotenv::dotenv;
use reqwest::blocking::Client;
use serde::Deserialize;
use std::env;
use std::io;

#[derive(Debug, Deserialize)]
struct WeatherResponse {
    location: Location,
    current: CurrentWeather,
}

#[derive(Debug, Deserialize)]
struct Location {
    name: String,
    region: String,
    country: String,
}

#[derive(Debug, Deserialize)]
struct CurrentWeather {
    temp_c: f64,
    condition: Condition,
    humidity: i32,
    feelslike_c: f64,
}

#[derive(Debug, Deserialize)]
struct Condition {
    text: String,
}  

fn cargar_configuracion() {
    // Cargar el archivo .env adecuado según RUST_ENV
    let env_mode = env::var("RUST_ENV").unwrap_or_else(|_| "development".to_string());

    let env_file = match env_mode.as_str() {
        "production" => ".env.production",
        _ => ".env.development", // Por defecto usar desarrollo
    };

    // Intentar cargar el archivo de entorno específico
    if dotenv::from_filename(env_file).is_err() {
        // Si falla, intentar con el .env genérico
        dotenv().ok();
    }

    println!("Modo de ejecución: {}", env_mode);

    // Mostrar información de depuración si está habilitado
    if env::var("DEBUG").unwrap_or_else(|_| "false".to_string()) == "true" {
        println!("Modo de depuración activado");
        println!(
            "Nivel  de log: {}",
            env::var("LOG_LEVEL").unwrap_or_else(|_| "info".to_string())
        );
    }
}

fn main() {
    // Cargar variables de entorno desde el archivo correspondiente
    cargar_configuracion();

    println!("Aplicación de Clima");
    println!("-----------------");
    println!("Ingresa el nombre de una ciudad:");

    let mut ciudad = String::new();
    io::stdin()
        .read_line(&mut ciudad)
        .expect("Error al leer la entrada");

    let ciudad = ciudad.trim();

    match obtener_clima(ciudad) {
        Ok(tiempo) => mostrar_clima(tiempo),
        Err(err) => println!("Error: {}", err),
    }
}

fn obtener_clima(ciudad: &str) -> Result<WeatherResponse, reqwest::Error> {
    // Obtener la clave API desde las variables de entorno
    let api_key = env::var("WEATHER_API_KEY")
        .expect("No se encontró la variable WEATHER_API_KEY en el archivo .env");

    let url = format!(
        "https://api.weatherapi.com/v1/current.json?key={}&q={}&lang=es",
        api_key, ciudad
    );

    // Mostrar la URL en modo depuración
    if env::var("DEBUG").unwrap_or_else(|_| "false".to_string()) == "true" {
        println!("DEBUG: Consultando URL: {}", url);
    }

    let client = Client::new();
    let response = client.get(&url).send()?.json::<WeatherResponse>()?;

    Ok(response)
}

fn mostrar_clima(tiempo: WeatherResponse) {
    println!("\nClima actual en {}:", tiempo.location.name);
    println!("País: {}", tiempo.location.country);
    println!("Región: {}", tiempo.location.region);
    println!("Temperatura: {:.1}°C", tiempo.current.temp_c);
    println!("Sensación térmica: {:.1}°C", tiempo.current.feelslike_c);
    println!("Humedad: {}%", tiempo.current.humidity);
    println!("Condición: {}", tiempo.current.condition.text);
}
