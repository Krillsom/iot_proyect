# Multi-Gateway MQTT Subscriber
# Inicia 2 procesos en paralelo para consumir G1 y G2

Write-Host "🚀 Iniciando consumidores MQTT para G1 y G2..." -ForegroundColor Green

# Subscriber G1
Start-Job -Name "MQTT_G1" -ScriptBlock {
    Set-Location "C:\Users\kevin\desarrollo\iot_dashboard\iot_proyect"
    php artisan mqtt:subscribe --topic="/sur/g1/status"
} | Out-Null

# Subscriber G2
Start-Job -Name "MQTT_G2" -ScriptBlock {
    Set-Location "C:\Users\kevin\desarrollo\iot_dashboard\iot_proyect"
    php artisan mqtt:subscribe --topic="/sur/g2/status"
} | Out-Null

Write-Host "✅ Subscribers iniciados en background" -ForegroundColor Cyan
Write-Host ""
Write-Host "📊 Estado de Jobs:" -ForegroundColor Yellow
Get-Job | Format-Table -Property Id, Name, State

Write-Host ""
Write-Host "🔍 Para ver output en tiempo real:" -ForegroundColor Magenta
Write-Host "   Receive-Job -Name MQTT_G1 -Keep" -ForegroundColor White
Write-Host "   Receive-Job -Name MQTT_G2 -Keep" -ForegroundColor White
Write-Host ""
Write-Host "🛑 Para detener todos:" -ForegroundColor Red
Write-Host "   Get-Job | Stop-Job; Get-Job | Remove-Job" -ForegroundColor White
Write-Host ""

# Loop para mostrar output periódicamente
Write-Host "📡 Monitoreando... (Ctrl+C para salir)" -ForegroundColor Green
Write-Host ("=" * 80)

while ($true) {
    Start-Sleep -Seconds 3
    
    $g1Output = Receive-Job -Name "MQTT_G1" -Keep 2>$null | Select-Object -Last 5
    $g2Output = Receive-Job -Name "MQTT_G2" -Keep 2>$null | Select-Object -Last 5
    
    if ($g1Output) {
        Write-Host "`n[G1] " -ForegroundColor Blue -NoNewline
        $g1Output | ForEach-Object { Write-Host $_ }
    }
    
    if ($g2Output) {
        Write-Host "`n[G2] " -ForegroundColor Magenta -NoNewline
        $g2Output | ForEach-Object { Write-Host $_ }
    }
}
